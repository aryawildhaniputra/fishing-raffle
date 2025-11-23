<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Models\ParticipantGroup;
use App\Support\Constants\Constants;
use App\Support\Enums\ConfirmDrawTypeEnum;
use App\Support\Enums\ParticipantGroupRaffleStatusEnum;
use App\Support\Enums\ParticipantGroupStatusEnum;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(string $ID)
    {
        $event = Event::with('groups')->withCount(['groups'])->find($ID);

        $groupCollect = collect($event->groups->toArray());
        $groups_not_yet_drawn = $groupCollect
            ->whereIn("status", ["dp", "paid"])
            ->where("raffle_status", "not_yet")
            ->sortBy([
                ['total_member', 'asc'],
                ['status', 'desc'],
                ['created_at', 'desc'],
            ])
            ->values();

        $groups_drawn = $groupCollect->where("raffle_status", "completed")->sortBy(["created_at", "asc"])->values();

        $eventParticipants = Participant::where('event_id', $event->id)->orderBy('stall_number', 'asc')->get();

        $participantsData = collect();

        for ($i = 0; $i < Constants::MAX_STALLS; $i++) {
            $stallNumber = $i + 1;
            $participantName = $eventParticipants->where('stall_number', $stallNumber)->first()?->name;
            $participantsData->push([
                "stall_number" => $stallNumber,
                "participant_name" => isset($participantName) ? $participantName : "-",
                "isBooked" => isset($participantName) ? true : false
            ]);
        }

        $allParticipantsData = $participantsData;

        $splitParticipantsData = $participantsData->split(2);

        return view('admin_views.detail_event', [
            'event' => $event,
            'groups_not_yet_drawn' => $groups_not_yet_drawn,
            'groups_drawn' => $groups_drawn,
            'participants' => $allParticipantsData,
            'rightColumnParticipant' => $splitParticipantsData->first()->sortByDesc('stall_number'),
            'leftColumnParticipant' => $splitParticipantsData->last(),
        ]);
    }

    public function storeParticipantGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'event_id' => 'required|string',
            'phone_num' => 'required|string',
            'total_member' => 'required|numeric|min:0',
            'status' => 'required|in:unpaid,dp,paid',
            'information' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->with('errors', join(', ', $validator->messages()->all()));
        }

        try {
            $data = $validator->safe()->all();

            $event = Event::find($data['event_id']);

            if (!isset($event)) {
                throw new Error("Data Tidak Ditemukan");
            }

            $existedParticipant = ParticipantGroup::where('event_id', $event->id)->where('name', $data['name'])->first();

            if ($existedParticipant) throw new Error("Nama Grup Telah Digunakan");

            $totalRegister = $event->total_registrant + $data['total_member'];

            if ($totalRegister > Constants::MAX_STALLS) {
                throw new Error("Kuota Lapak Tidak Mencukupi");
            }
            DB::beginTransaction();
            ParticipantGroup::create($data);
            $event->update([
                "total_registrant" => $totalRegister
            ]);
            DB::commit();

            return redirect()->back()->with('success', 'Data Pendaftar Berhasil Ditambahkan');
        } catch (\Throwable $th) {
            return back()
                ->with('errors', $th->getMessage());
        }
    }

    public function getParticipantGroupByID(string $ID)
    {
        $data = ParticipantGroup::find($ID);

        if (!$data) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function updateParticipantGroup(Request $request, string $ID)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'phone_num' => 'nullable|string',
            'total_member' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:' . ParticipantGroupStatusEnum::UNPAID->value . ',' . ParticipantGroupStatusEnum::DP->value . ',' . ParticipantGroupStatusEnum::PAID->value,
            'information' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->with('errors', join(', ', $validator->messages()->all()));
        }

        try {
            $newDataGroupParticipant = $validator->safe()->all();
            $newDataEvent = Collection::Make();

            $participantGroup = ParticipantGroup::find($ID);

            if (!$participantGroup) {
                throw new Error("Data Grup Tidak Ditemukan");
            }

            $event = Event::find($participantGroup->event_id);

            if (!isset($event)) {
                throw new Error("Data Event Tidak Ditemukan");
            }

            if ($participantGroup->status == ParticipantGroupStatusEnum::COMPLETED->value) {
                throw new Error("Data Yang sudah selesai diundi tidak dapat diedit");
            }

            if ($participantGroup->total_member != $newDataGroupParticipant['total_member']) {
                $NewTotalRegister = ($event->total_registrant - $participantGroup->total_member) + $newDataGroupParticipant['total_member'];

                if ($NewTotalRegister > Constants::MAX_STALLS) {
                    throw new Error("Kuota Lapak Tidak Mencukupi");
                }
                $newDataEvent->put('total_registrant', $NewTotalRegister);
            }


            DB::beginTransaction();
            $participantGroup->update($newDataGroupParticipant);

            if ($newDataEvent->count() > 0) {
                $event->update($newDataEvent->toArray());
            }
            DB::commit();

            return redirect()->back()->with('success', 'Data Pendaftar Berhasil Diperbarui');
        } catch (\Throwable $th) {
            return back()
                ->with('errors', $th->getMessage());
        }
    }

    public function destroyParticipantGroupByID(string $ID)
    {
        try {
            $participantGroup = ParticipantGroup::find($ID);

            if (!isset($participantGroup)) {
                throw new Error("Data Tidak Ditemukan");
            }

            $participantGroup->delete();

            return redirect()->back()->with('success', 'Data Pendaftar Telah Dihapus');
        } catch (\Throwable $th) {
            return back()
                ->with('errors', 'Gagal Menghapus Data Pendaftar');
        }
    }

    public function drawStall(string $ID)
    {
        try {
            $participantGroup = ParticipantGroup::find($ID);

            if (!$participantGroup) throw new Error("Data Grup Tidak Ditemukan");

            if ($participantGroup->status === ParticipantGroupRaffleStatusEnum::COMPLETED->value) throw new Error("Grup Telah Diundi");

            $bookedStalls = Participant::where('event_id', $participantGroup->event_id)->get()->pluck('stall_number');

            $numbers = collect(range(1, Constants::MAX_STALLS));

            $availableStallNumber = $numbers->whereNotIn(null, $bookedStalls->toArray())->values();

            $rawRange = $participantGroup->total_member > 1 ? $availableStallNumber->chunk(($participantGroup->total_member * 2) - 1) : $availableStallNumber;

            $randomStallNumber = $participantGroup->total_member > 1 ?
                $rawRange->filter(function ($data) use ($participantGroup) {
                    return $data->count() >= $participantGroup->total_member;
                })->random()->values()
                : $rawRange->random();


            $under = $participantGroup->total_member > 1 ? $randomStallNumber->slice(0, ceil($randomStallNumber->count() / 2))->values() : null;
            $upper = $participantGroup->total_member > 1 ? $randomStallNumber->slice(ceil($randomStallNumber->count() / 2) - 1)->values() : null;
            $middle = $participantGroup->total_member > 1 ? $under->last() : $randomStallNumber;


            if ($participantGroup->total_member > 1) {
                if ($upper->count() < $participantGroup->total_member || $under->count() < $participantGroup->total_member) {
                    $randomStallNumberChunk = $randomStallNumber->chunk($participantGroup->total_member);
                    $randomStallNumberChunkFilter = $randomStallNumberChunk->filter(function ($data) use ($participantGroup) {
                        return count($data) >= $participantGroup->total_member;
                    })->first();

                    $under = $randomStallNumberChunkFilter;
                    $upper = $randomStallNumberChunkFilter;
                    $middle = $randomStallNumberChunkFilter->split(2)->first()->last();
                }
            }

            return response()->json([
                'data' => [
                    'participant_group_id' => $participantGroup->id,
                    'total_member' => $participantGroup->total_member,
                    'randomStallNumber' => $randomStallNumber,
                    'upper' => $upper,
                    'under' => $under,
                    'middle' => $middle,
                ],
            ]);
        } catch (\Throwable $th) {
            dd($th->getMessage());
            return response()->json([
                'message' => 'Error'
            ], 404);
        }
    }

    public function confirmDraw(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'participantGroupID' => 'required|string',
                'randomStallNumberType' => 'nullable|string',
                'randomStallNumber' => 'required|string',
                'randomStallNumberUpper' => 'nullable|string',
                'randomStallNumberUnder' => 'nullable|string',
            ]);

            if ($validator->fails()) throw new Error(join(', ', $validator->messages()->all()));

            $randomStallNumber = explode(',', $request->randomStallNumber);

            if (count($randomStallNumber) > 1) {
                $validator = Validator::make($request->all(), [
                    'randomStallNumberType' => 'required|string|in:' . ConfirmDrawTypeEnum::UPPER->value . ',' . ConfirmDrawTypeEnum::UNDER->value,
                    'randomStallNumberUpper' => 'required|string',
                    'randomStallNumberUnder' => 'required|string',
                ]);

                if ($validator->fails()) throw new Error(join(', ', $validator->messages()->all()));
            }

            $participantGroup = ParticipantGroup::find($request->participantGroupID);

            if (!$participantGroup) throw new Error("Data Grup Tidak Ditemukan");

            if ($participantGroup->status === ParticipantGroupRaffleStatusEnum::COMPLETED->value) throw new Error("Grup Telah Diundi");

            DB::beginTransaction();

            $typeDrawConfirm = $request->randomStallNumberType;
            $randomStallNumberConfirmation = null;

            if (count($randomStallNumber) > 1) {
                if ($typeDrawConfirm == ConfirmDrawTypeEnum::UPPER->value) {
                    $randomStallNumberConfirmation = json_decode($request->randomStallNumberUpper, true);
                } else if ($typeDrawConfirm == ConfirmDrawTypeEnum::UNDER->value) {
                    $randomStallNumberConfirmation = json_decode($request->randomStallNumberUnder, true);
                } else {
                    if (!$participantGroup) throw new Error("Type Draw Confirm Invalid");
                }
            } else {
                $randomStallNumberConfirmation = $randomStallNumber;
            }


            for ($i = 0; $i < count($randomStallNumberConfirmation); $i++) {
                $isStallBooked = Participant::where('event_id', $participantGroup->event_id)
                    ->where('stall_number', $randomStallNumberConfirmation[$i])
                    ->get();

                if ($isStallBooked->count() > 0) throw new Error("Lapak " . $randomStallNumberConfirmation[$i] . " Telah Dibooking");

                Participant::create([
                    'name' => $participantGroup->name . "-" . ($i + 1),
                    'participant_groups_id' => $participantGroup->id,
                    'event_id' => $participantGroup->event_id,
                    'stall_number' => $randomStallNumberConfirmation[$i],
                ]);
            }

            $participantGroup->update([
                "raffle_status" => ParticipantGroupRaffleStatusEnum::COMPLETED->value
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data Undian Berhasil Disimpan');
        } catch (\Throwable $th) {
            DB::rollback();

            return back()
                ->with('errors', $th->getMessage());
        }
    }
}
