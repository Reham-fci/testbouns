<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Salesperson;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function App\CPU\translate;

class SalespersonController extends Controller
{
    public function index()
    {
        return view('admin-views.Salesperson.index');
    }

    public function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $delivery_men = Salesperson::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $delivery_men = new Salesperson();
        }
        
        $delivery_men = $delivery_men->latest()->where(['seller_id' => 0])->paginate(25)->appends($query_param);
        return view('admin-views.Salesperson.list', compact('delivery_men', 'search'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $delivery_men = Salesperson::where(['seller_id' => 0])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('identity_number', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.Salesperson.partials._table', compact('delivery_men'))->render()
        ]);
    }

    public function preview($id)
    {
        $dm = Salesperson::with(['reviews'])->where(['id' => $id])->first();
        return view('admin-views.Salesperson.view', compact('dm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'email' => 'required|unique:delivery_men',
            'phone' => 'required|unique:delivery_men',
        ], [
            'f_name.required' => 'First name is required!'
        ]);

        
        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                array_push($id_img_names, ImageManager::upload('Salesperson/', 'png', $img));
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $dm = new Salesperson();
        $dm->seller_id = 0;
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->identity_image = $identity_image;
        $dm->image = ImageManager::upload('Salesperson/', 'png', $request->file('image'));
        $dm->password = bcrypt($request->password);
        $dm->save();

        Toastr::success('Delivery-man added successfully!');
        return redirect('admin/Salesperson/list');
    }

    public function edit($id)
    {
        $delivery_man = Salesperson::find($id);
        return view('admin-views.Salesperson.edit', compact('delivery_man'));
    }

    public function status(Request $request)
    {
        $delivery_man = Salesperson::find($request->id);
        $delivery_man->is_active = $request->status;
        $delivery_man->save();
        return response()->json([], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required|email|unique:delivery_men,email,'.$id,
            'phone' => 'required|unique:delivery_men,phone,'.$id,
        ], [
            'f_name.required' => 'First name is required!'
        ]);

        $delivery_man = Salesperson::where(['id' => $id, 'seller_id' => 0])->first();
        if (isset($delivery_man) && $request['email'] != $delivery_man['email']) {
            $request->validate([
                'email' => 'required|unique:delivery_men',
            ]);
        }

        if (!empty($request->file('identity_image'))) {
            foreach (json_decode($delivery_man['identity_image'], true) as $img) {
                if (Storage::disk('public')->exists('Salesperson/' . $img)) {
                    Storage::disk('public')->delete('Salesperson/' . $img);
                }
            }
            $img_keeper = [];
            foreach ($request->identity_image as $img) {
                array_push($img_keeper, ImageManager::upload('Salesperson/', 'png', $img));
            }
            $identity_image = json_encode($img_keeper);
        } else {
            $identity_image = $delivery_man['identity_image'];
        }
        $delivery_man->seller_id = 0;
        $delivery_man->f_name = $request->f_name;
        $delivery_man->l_name = $request->l_name;
        $delivery_man->email = $request->email;
        $delivery_man->phone = $request->phone;
        $delivery_man->identity_number = $request->identity_number;
        $delivery_man->identity_type = $request->identity_type;
        $delivery_man->identity_image = $identity_image;
        $delivery_man->image = $request->has('image') ? ImageManager::update('Salesperson/', $delivery_man->image, 'png', $request->file('image')) : $delivery_man->image;
        $delivery_man->password = strlen($request->password) > 1 ? bcrypt($request->password) : $delivery_man['password'];
        $delivery_man->save();

        Toastr::success('Delivery-man updated successfully!');
        return redirect('admin/Salesperson/list');
    }

    public function delete(Request $request)
    {
        $delivery_man = Salesperson::find($request->id);
        if (Storage::disk('public')->exists('Salesperson/' . $delivery_man['image'])) {
            Storage::disk('public')->delete('Salesperson/' . $delivery_man['image']);
        }

        foreach (json_decode($delivery_man['identity_image'], true) as $img) {
            if (Storage::disk('public')->exists('Salesperson/' . $img)) {
                Storage::disk('public')->delete('Salesperson/' . $img);
            }
        }

        $delivery_man->delete();
        Toastr::success(translate('Delivery-man removed!'));
        return back();
    }
}
