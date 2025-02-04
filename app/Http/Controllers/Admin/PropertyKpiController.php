<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

use App\Models\admin\Listing;
use App\Models\admin\PropertyKpi;
use Illuminate\Http\Request;

class PropertyKpiController extends Controller
{
    // Fetch KPIs for a listing
    public function show($listing_id)
    {
        $kpi = PropertyKpi::where('listing_id', $listing_id)->get();
        if (!$kpi) {
            return response()->json(['message' => 'KPIs not found'], 404);
        }
        return response()->json($kpi);
    }


    public function getAcceptedOfferPercentage($listing_id)
{
    $offerData = DB::table('offers')
        ->select(
            DB::raw('COUNT(listing_id) as total_offers'),
            DB::raw('SUM(CASE WHEN status = "Accepted" THEN 1 ELSE 0 END) as accepted_offers'),
            DB::raw('(SUM(CASE WHEN status = "Accepted" THEN 1 ELSE 0 END) / COUNT(listing_id)) * 100 as accepted_percentage')
        )
        ->where('listing_id', $listing_id)
        ->first();

        $allOffers = DB::table('offers')
        ->where('listing_id', $listing_id)
        ->get();


        return response()->json([$offerData,$allOffers]);
}




    // Update KPIs (e.g., increment views, clicks, etc.)
    public function update(Request $request, $listing_id)
    {
        $kpi = PropertyKpi::updateOrCreate(
            ['listing_id' => $listing_id],
            [
                'views' => $request->views ?? 0,
                'inquiries' => $request->inquiries ?? 0,
                'clicks' => $request->clicks ?? 0,
                'conversion_rate' => $request->conversion_rate ?? 0,
            ]
        );

        return response()->json(['message' => 'KPIs updated successfully', 'kpi' => $kpi]);
    }
}
