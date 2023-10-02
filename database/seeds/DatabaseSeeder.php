<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(ApplicationsSeeder::class);
        $this->call(ModulesSeeder::class);
        //$this->call(SauConfigurationSeeder::class);
        $this->call(MakeAllPermissionsSeeder::class);
        $this->call(DmQualificationsSeeder::class);
        $this->call(MakeCustomPermissionsSeeder::class);
        $this->call(CreateRoleSuperAdminSeeder::class);
        $this->call(MakeCustomRolesDefinedSeeder::class);
        $this->call(CtActionPlanDefaultSeeder::class);
        $this->call(CtQualificationsSeeder::class);
        $this->call(CtSectionCategoryItemsSeeder::class);
        $this->call(CtStandardClassificationSeeder::class);
        $this->call(ctItemActivitiesSeeder::class);
        $this->call(LmLawsTypesSeeder::class);
        $this->call(LmFulfillmentValuesSeeder::class);
        $this->call(ModulesDependenciesSeeder::class);
        $this->call(MakeKeywordsSeeder::class);
        $this->call(CtContractHighRiskTypeSeeder::class);
        $this->call(CtTrainingTypesQualificationsSeeder::class);
        $this->call(PhQualificationsInspectionsSeeder::class);
        $this->call(PhTypeInspectionsSeeder::class);
        $this->call(CreatedApiTokenUsersSeeder::class);
        $this->call(AccidentAnalisysCausesSeeder::class);
    }
}
