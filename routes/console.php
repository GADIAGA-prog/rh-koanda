<?php

use Illuminate\Support\Facades\Schedule;

// Alertes RH quotidiennes (tout le groupe, hors scope filiale).
Schedule::command('rh:contrats-expirants')->dailyAt('07:00');
Schedule::command('rh:expirer-contrats')->dailyAt('00:30');
