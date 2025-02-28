<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactForm;

class ContactFormController extends Controller {

    public function index() {
        return response()->json(ContactForm::all());
    }


    public function show($id) 
    {
        $contact_form = ContactForm::find($id);
        if ($contact_form) {
            return response()->json($contact_form);
        } else {
            return response()->json(['message' => 'Contact form not found'], 404);
        }
    }
    
    public function store(Request $request) {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'help_type' => 'required|in:Buying,Selling,Both Buying and Selling',
            'timeline' => 'required|in:0-3 Months,3-6 Months,6-12 Months',
            'message' => 'nullable|string',
        ]);

        $contact = ContactForm::create($request->all());

        return response()->json([
            'message' => 'Form submitted successfully!',
            'data' => $contact
        ], 201);
    }
}
