<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Lead;
use App\Models\admin\LeadHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    /**
     * Get all leads.
     */
    public function index()
    {
        $leads = Lead::with(['addedBy', 'assignedTo','leadSource', 'leadType', 'leadHistories'])->get();
        return response()->json($leads);
    }

    /**
     * Store a new lead.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'assigned_to' => 'required|exists:users,id',
            'lead_type_id' => 'required|exists:lead_types,id',
            'status' => 'in:open,in_progress,closed,rejected',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tags' => 'nullable|array',
            'source' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'lead_value' => 'nullable|numeric',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',
            'default_language' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contacted_today' => 'nullable|boolean',
            'special_notes' => 'nullable|string',
            'lead_source_id'=> 'nullable|exists:lead_sources,id',
        ]);

        $validatedData['added_by'] = Auth::id();
        $lead = Lead::create($validatedData);

        return response()->json(['message' => 'Lead created successfully', 'lead' => $lead], 201);
    }

    /**
     * Show a specific lead.
     */
    public function show($id)
    {
        $lead = Lead::with(['addedBy', 'assignedTo', 'leadType','leadSource', 'leadHistories'])->find($id);
          
        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }
        return response()->json($lead);
    }

    /**
     * Update a lead.
     */
    public function update(Request $request, $id)
    {
        $lead = Lead::find($id);
        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }
        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'lead_type_id' => 'nullable|exists:lead_types,id',
            'status' => 'nullable|in:open,in_progress,closed,rejected',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tags' => 'nullable|array',
            'source' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'lead_value' => 'nullable|numeric',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',
            'default_language' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contacted_today' => 'nullable|boolean',
            'special_notes' => 'nullable|string',
            'lead_source_id'=> 'nullable|exists:lead_sources,id',
        ]);

        $lead->update($validatedData);

        return response()->json(['message' => 'Lead updated successfully', 'lead' => $lead]);
    }

    /**
     * Delete a lead.
     */
    public function destroy($id)
    {
        $lead = Lead::find($id);id: 
        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }
        $lead->delete();
        return response()->json(['message' => 'Lead deleted successfully']);
    }

    /**
     * Assign a lead to a team member.
     */
    public function assignLead(Request $request, $id)
    {
        $validatedData = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $lead = Lead::findOrFail($id);
        $lead->update(['assigned_to' => $validatedData['assigned_to']]);

        return response()->json(['message' => 'Lead assigned successfully', 'lead' => $lead]);
    }

    /**
     * Add lead history (Contact details, Follow-up, etc.).
     */
    public function addHistory(Request $request, $lead_id)
    {
        $validatedData = $request->validate([
            'contact_date' => 'required|date',
            'note' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'status' => 'in:contacted,no_response,interested,rejected',
        ]);
        $lead = Lead::find($lead_id);

        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        $history = LeadHistory::create([
            'lead_id' => $lead_id,
            'contact_date' => $validatedData['contact_date'],
            'note' => $validatedData['note'],
            'follow_up_date' => $validatedData['follow_up_date'],
        ]);

        return response()->json(['message' => 'Lead history added successfully', 'history' => $history]);
    }



    public function updateHistory(Request $request, $lead_id, $history_id)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'contact_date' => 'required|date',
            'note' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'status' => 'in:open,in_progress,closed,rejected',
        ]);
    
        // Find the lead
        $lead = Lead::find($lead_id);
    
        // Check if the lead exists
        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }
    
        // Find the history record by lead_id and history_id
        $history = LeadHistory::where('lead_id', $lead_id)->find($history_id);
    
        // Check if the history record exists
        if (!$history) {
            return response()->json(['message' => 'Lead history not found'], 404);
        }
    
        // Update the history record with validated data
        $history->update([
            'contact_date' => $validatedData['contact_date'],
            'note' => $validatedData['note'],
            'follow_up_date' => $validatedData['follow_up_date'],
            'status' => $validatedData['status'],
        ]);
    
        return response()->json(['message' => 'Lead history updated successfully', 'history' => $history]);
    }
    


    public function ViewHistoryLeads($id)
    {
        $lead = Lead::find($id);
        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }
        $leadHistories = $lead->leadHistories;
        return response()->json([$leadHistories],200);

    }
}
