<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ParticipantGroup;
use App\Support\Constants\Constants;
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

        return view('admin_views.detail_event', ['event' => $event]);
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

            $event = Event::find($participantGroup->event_id);

            if (!isset($event)) {
                throw new Error("Data Event Tidak Ditemukan");
            }

            DB::beginTransaction();

            $newTotalRegister = max(
                0,
                $event->total_registrant - $participantGroup->total_member
            );

            $event->update([
                'total_registrant' => $newTotalRegister
            ]);

            $participantGroup->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Data Pendaftar Telah Dihapus');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return back()
                ->with('errors', 'Gagal Menghapus Data Pendaftar');
        }
    }
}

