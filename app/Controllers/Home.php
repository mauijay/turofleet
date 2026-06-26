<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('fleet_command_center/index', [
            'commandCenter' => service('fleetCommandCenterViewModelService')->forToday(),
            'assets' => service('assetManifestService')->appAssets(),
        ]);
    }
}
