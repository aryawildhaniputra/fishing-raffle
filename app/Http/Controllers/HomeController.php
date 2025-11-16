<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        $events = Event::OrderBy('event_date', 'desc')->get();

        return view('admin_views.home_page', ["events" => $events]);
    }

    public function storeEvent(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'event_date' => 'required|date',
                'price' => 'required|numeric|min:0',
                'total_stalls' => 'required|numeric|min:0|max:222',
            ],
            [
                'name.required' => "Nama Event Wajib Diisi",
                'event_date.required' => "Tanggal Event Wajib Diisi",
                'price.required' => "Harga Tiket Event Wajib Diisi",
                'total_stalls.required' => "Jumlah Lapak Event Wajib Diisi",
            ]
        );

        if ($validator->fails()) {
            return back()
                ->with('errors', join(', ', $validator->messages()->all()));
        }

        try {
            $data = $validator->safe()->all();
            $data['total_registrant'] = 0;

            Event::create($data);
            return redirect()->back()->with('success', 'Data Event Berhasil Ditambahkan');
        } catch (\Throwable $th) {
            return back()
                ->with('errors', $th->getMessage());
        }
    }

    public function updateEvent(Request $request, string $ID)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'event_date' => 'date',
            'price' => 'numeric|min:0',
            'total_stalls' => 'numeric|min:0|max:222',
        ]);

        if ($validator->fails()) {
            return back()
                ->with('errors', join(', ', $validator->messages()->all()));
        }

        try {
            $data = $validator->safe()->all();

            $event = Event::find($ID);

            if (!isset($event)) {
                throw new Error("Data Tidak Ditemukan");
            }

            if ($event->total_stalls != $data['total_stalls']) {
                $newTotalRegistrant = $data['total_stalls'] - $event->total_registrant;

                if ($newTotalRegistrant < 0) {
                    throw new Error("Data Pendaftar Lebih Banyak daripada Data Lapak Baru");
                }
            }
            $event->update($data);
            return redirect()->back()->with('success', 'Data Event Berhasil Ditambahkan');
        } catch (\Throwable $th) {
            return back()
                ->with('errors', $th->getMessage());
        }
    }

    public function destroyEvent(string $ID)
    {
        try {
            $event = Event::find($ID);

            if (!isset($event)) {
                throw new Error("Data Tidak Ditemukan");
            }

            $event->delete();

            return redirect()->back()->with('success', 'Data Event Dihapus');
        } catch (\Throwable $th) {
            return back()
                ->with('errors', 'Gagal Menghapus Data Event');
        }
    }

    public function editProfile()
    {
        return view('admin_views.edit_profile');
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'nullable',
            'new_password' => 'nullable|min:6',
            'confirm_password' => 'required_with:new_password|same:new_password',
        ], [
            'new_password.min' => 'Password minimal harus :min karakter',
            'confirm_password.same' => 'Pengulangan Password Tidak Sama',
        ]);

        if ($validator->fails()) {
            return back()
                ->with('errors', join(', ', $validator->messages()->all()))
                ->withInput();
        }


        try {
            $reqData = $validator->safe()->all();

            $oldData = User::find(Auth::user()->id);
            if (!isset($oldData)) throw new Exception('Data Tidak Ditemukan');

            $newData = collection::make();

            if (isset($reqData['old_password']) && isset($reqData['new_password'])) {
                if (!(Hash::check($reqData['old_password'], $oldData->password))) {
                    throw new Exception('Password Lama Tidak Valid');
                } else {
                    $newData->put('password', bcrypt($reqData['new_password']));
                }
            }

            $oldData->update($newData->toArray());

            return redirect()->route('admin.home');
        } catch (\Throwable $th) {
            return back()
                ->with('errors', 'Gagal Menghapus Data Event');
        }
    }
}
