<?php

namespace App\Http\Controllers;

use App\Drug;
use App\Prescription;
use App\Prescription_drug;
use App\Prescription_test;
use App\Test;
use App\User;
use Illuminate\Http\Request;
<<<<<<< HEAD
use App\Http\Controllers\PDF;
use Illuminate\Support\Facades\Auth;
=======
>>>>>>> 6190a01e79f7451fa92dc9de291766649b51145c

class PrescriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $drugs = Drug::all();
        $patients = User::where('role_id', '3')->get();
        $praticiens = User::where('role_id', '2')->get();
        $tests = Test::all();

        return view('prescription.create', compact('drugs', 'patients', 'praticiens', 'tests'));
    }

    public function create_By_Id($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'The user does not exist');
        }
        $drugs = Drug::all();
        $patients = User::where('role_id', '3')->get();
        $praticiens = User::where('role_id', '2')->get();
<<<<<<< HEAD
        // $tests = Test::where('user_id', $id)->get();
        $tests = Test::where('user_id', $id)
             ->whereDoesntHave('Prescription') // Prescription correspond a la fonction définie dans le model test
             ->get();
=======
        $tests = Test::where('user_id', $id)->get();
>>>>>>> 6190a01e79f7451fa92dc9de291766649b51145c

        return view('prescription.create_By_user', ['userId' => $id, 'userName' => $user->name], compact('drugs', 'patients', 'praticiens', 'tests'));
    }

    public function follow($id)
    {
        $prescription = Prescription::findOrfail($id);
        $prescription_drugs = Prescription_drug::where('prescription_id', $id)->get();
        $drugs = Drug::all();

        return view('prescription.follow', ['prescription' => $prescription, 'prescription_drugs' => $prescription_drugs, 'drugs' => $drugs]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => ['required', 'exists:users,id'],
            'Doctor_id' => ['required', 'exists:users,id'],
            'nom' => ['required'],
            'trade_name.*' => 'required',
        ]);

        $prescription = new Prescription();

        $prescription->user_id = $request->patient_id;
<<<<<<< HEAD
        $prescription->doctor_id = Auth::user()->id;
        $prescription->reference = 'p' . rand(10000, 99999);
        $prescription->nom = $request->nom;
=======
        $prescription->doctor_id = $request->Doctor_id;
        $prescription->reference = 'p'.rand(10000, 99999);

>>>>>>> 6190a01e79f7451fa92dc9de291766649b51145c
        $prescription->save();

        if (isset($request->trade_name)) {
            $i = count($request->trade_name);

            for ($x = 0; $x < $i; ++$x) {
                if ($request->trade_name[$x] != null) {
                    $add_drug = new Prescription_drug();

                    $add_drug->type = $request->input('type.'.$x) ?? null;
                    $add_drug->strength = $request->input('strength.'.$x) ?? null;
                    $add_drug->dose = $request->input('dose.'.$x) ?? null;
                    $add_drug->duration = $request->input('duration.'.$x) ?? null;
                    $add_drug->drug_advice = $request->input('drug_advice.'.$x) ?? null;
                    $add_drug->prescription_id = $prescription->id;
                    $add_drug->drug_id = $request->input('trade_name.'.$x) ?? null;

                    $add_drug->save();
                }
            }
        }

        if (isset($request->test_name)) {
            $y = count($request->test_name);

            for ($x = 0; $x < $y; ++$x) {
                $add_test = new Prescription_test();

                $add_test->test_id = $request->test_name[$x];
                $add_test->prescription_id = $prescription->id;
                $add_test->description = $request->description[$x];

                $add_test->save();
            }
        }

        return \Redirect::route('prescription.all')->with('success', 'Prescription Created Successfully!');
    }

    public function all()
    {
        $sortColumn = request()->get('sort');
        $sortOrder = request()->get('order', 'asc');

        // Define a default sort column and order
        $defaultSortColumn = 'id';
        $defaultSortOrder = 'asc';

        // Define a list of valid sort columns
        $validSortColumns = ['id', 'created_at'];

        // Check if the requested sort column is valid, otherwise use the default
        if (!in_array($sortColumn, $validSortColumns)) {
            $sortColumn = $defaultSortColumn;
        }

        $user = Auth::user();
        $doctorId = $user->id;

        // Perform a join with the 'users' table to get the patient names
        $prescriptions = Prescription::select('prescriptions.*', 'users.name as patient_name')
        ->join('users', 'prescriptions.user_id', '=', 'users.id')
        ->when($user->role_id !== 1, function ($query) use ($doctorId) {
            // Add a condition to filter prescriptions for non-admin users
            $query->where('prescriptions.doctor_id', $doctorId);
        })
        ->orderBy($sortColumn, $sortOrder)
        ->paginate(25);
        return view('prescription.all', ['prescriptions' => $prescriptions]);
    }

    public function view($id)
    {
        $prescription = Prescription::findOrfail($id);
        $prescription_drugs = Prescription_drug::where('prescription_id', $id)->get();
        $prescription_tests = Prescription_test::where('prescription_id', $id)->get();

        return view('prescription.view', ['prescription' => $prescription, 'prescription_drugs' => $prescription_drugs, 'prescription_tests' => $prescription_tests]);
    }

    public function pdf($id)
    {
        $prescription = Prescription::findOrfail($id);
        $prescription_drugs = Prescription_drug::where('prescription_id', $id)->get();

        view()->share(['prescription' => $prescription, 'prescription_drugs' => $prescription_drugs]);

        $pdf = PDF::loadView('prescription.pdf_view', ['prescription' => $prescription, 'prescription_drugs' => $prescription_drugs]);
        $pdf->setOption('viewport-size', '1024x768');

        // download PDF file with download method
        return $pdf->download($prescription->User->name.'_pdf.pdf');
    }

    public function edit($id)
    {
        $prescription = Prescription::findOrfail($id);
        $prescription_drugs = Prescription_drug::where('prescription_id', $id)->get();
        $prescription_tests = Prescription_test::where('prescription_id', $id)->get();

        $drugs = Drug::all();
        $tests = Test::all();

        return view('prescription.edit', ['prescription' => $prescription, 'prescription_drugs' => $prescription_drugs, 'prescription_tests' => $prescription_tests, 'drugs' => $drugs, 'tests' => $tests]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => ['required', 'exists:users,id'],
            'trade_name.*' => 'required',
        ]);

        $prescription_drugs = Prescription_drug::where('prescription_id', $request->prescription_id)->pluck('id')->toArray();

        if ($request->has('prescription_drug_id')) {
            $filtered = $request->prescription_drug_id;
        } else {
            $filtered = [];
        }

        foreach ($prescription_drugs as $key => $dz) {
            $filtered[] = "$dz";
        }

        $filtered_unique = array_unique($filtered);

        $deleted_drugs = array_count_values($filtered);

        foreach ($deleted_drugs as $key => $value) {
            if ($value < 2) {
                $new_array[] = $key;

                Prescription_drug::destroy($key);
            }
        }

        if (isset($request->trade_name)) {
            $i = count($request->trade_name);

            for ($x = 0; $x < $i; ++$x) {
                if (isset($request->prescription_drug_id[$x])) {
                    Prescription_drug::where('id', $request->prescription_drug_id[$x])
                        ->update([
                            'type' => isset($request->type[$x]) ? $request->type[$x] : null,
                            'strength' => isset($request->strength[$x]) ? $request->strength[$x] : null,
                            'dose' => isset($request->dose[$x]) ? $request->dose[$x] : null,
                            'duration' => isset($request->duration[$x]) ? $request->duration[$x] : null,
                            'drug_advice' => isset($request->drug_advice[$x]) ? $request->drug_advice[$x] : null,
                            'drug_id' => isset($request->trade_name[$x]) ? $request->trade_name[$x] : null,
                        ]);
                } else {
                    $add_drug = new Prescription_drug();

                    $add_drug->type = $request->type[$x] ?? null;
                    $add_drug->strength = $request->strength[$x] ?? null;
                    $add_drug->dose = $request->dose[$x] ?? null;
                    $add_drug->duration = $request->duration[$x] ?? null;
                    $add_drug->drug_advice = $request->drug_advice[$x] ?? null;
                    $add_drug->prescription_id = $request->prescription_id ?? null;
                    $add_drug->drug_id = $request->trade_name[$x] ?? null;

                    $add_drug->save();
                }
            }
        }

        // Test

        $prescription_tests = Prescription_test::where('prescription_id', $request->prescription_id)->pluck('id')->toArray();

        if ($request->has('prescription_test_id')) {
            $filtered_test = $request->prescription_test_id;
        } else {
            $filtered_test = [];
        }

        foreach ($prescription_tests as $key => $fr) {
            $filtered_test[] = "$fr";
        }

        $filtered_test_unique = array_unique($filtered_test);

        $deleted_tests = array_count_values($filtered_test);

        foreach ($deleted_tests as $key => $value) {
            if ($value < 2) {
                // $new_array[] = $key;
                Prescription_test::destroy($key);
            }
        }

        if (isset($request->test_name)) {
            $i = count($request->test_name);

            for ($x = 0; $x < $i; ++$x) {
                if (isset($request->prescription_test_id[$x])) {
                    Prescription_test::where('id', $request->prescription_test_id[$x])
                        ->update([
                            'description' => $request->description[$x],
                            'test_id' => $request->test_name[$x],
                        ]);
                } else {
                    $add_test = new Prescription_test();
                    $add_test->description = $request->description[$x];
                    $add_test->prescription_id = $request->prescription_id;
                    $add_test->test_id = $request->test_name[$x];

                    $add_test->save();
                }
            }
        }

        return \Redirect::route('prescription.view', ['id' => $request->prescription_id])->with('success', 'Prescription Edited Successfully!');

        // return $request;
    }

    public function destroy($id)
    {
        Prescription::destroy($id);

        return \Redirect::route('prescription.all')->with('success', 'Prescription Deleted Successfully!');
    }

    public function view_for_user(Request $request, $id)
    {
        $User = User::findOrfail($id);

        $prescriptions = Prescription::where('user_id', $id)->paginate(25);

        return view('prescription.view_for_user', ['prescriptions' => $prescriptions]);
    }
}
