<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->renameColumn('Owner_Full_Name', 'owner_full_name');
            $table->renameColumn('Owner_Age', 'owner_age');
            $table->renameColumn('Owner_Contact_Number', 'owner_contact_number');
            $table->renameColumn('Owner_Email_Address', 'owner_email_address');
            $table->renameColumn('Owner_Government_ID_Proof', 'owner_government_id_proof');
            $table->renameColumn('Owner_Property_Ownership_Proof', 'owner_property_ownership_proof');
            $table->renameColumn('Owner_Ownership_Type', 'owner_ownership_type');
            $table->renameColumn('Owner_Property_Documents', 'owner_property_documents');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->renameColumn('owner_full_name', 'Owner_Full_Name');
            $table->renameColumn('owner_age', 'Owner_Age');
            $table->renameColumn('owner_contact_number', 'Owner_Contact_Number');
            $table->renameColumn('owner_email_address', 'Owner_Email_Address');
            $table->renameColumn('owner_government_id_proof', 'Owner_Government_ID_Proof');
            $table->renameColumn('owner_property_ownership_proof', 'Owner_Property_Ownership_Proof');
            $table->renameColumn('owner_ownership_type', 'Owner_Ownership_Type');
            $table->renameColumn('owner_property_documents', 'Owner_Property_Documents');
      
        });
    }
};
