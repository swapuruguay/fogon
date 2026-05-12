<?php

use App\Core\Controller;
use App\Core\Session;

class indexController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index(): void {
        $this->requireAuth();
        $this->_view->titulo = 'Dashboard';
        $this->_view->_route = 'dashboard';

        $modelSocios = $this->loadModel('socios');
        $modelMovimientos = $this->loadModel('movimientos');

        $allSocios = $modelSocios->getAll();
        $inHouse = array_filter($allSocios, fn($s) => $s->isInHouse());
        $street = array_filter($allSocios, fn($s) => !$s->isInHouse());

        $this->_view->inHouseCount = count($inHouse);
        $this->_view->streetCount = count($street);
        $this->_view->totalMembers = count($allSocios);

        $lastEmision = $modelMovimientos->lastEmision();
        $this->_view->currentMonth = $lastEmision 
            ? date('M Y', strtotime($lastEmision->fecha_generado)) 
            : date('M Y');
        $this->_view->monthGenerated = !empty($lastEmision);

        if ($lastEmision) {
            $pagosStats = $modelMovimientos->getPaidCountByType((int)$lastEmision->anio, (int)$lastEmision->mes);
            $this->_view->inHousePaid = $pagosStats['fogon_paid'];
            $this->_view->streetPaid = $pagosStats['street_paid'];
            $fogonTotal = $pagosStats['fogon_total'];
            $streetTotal = $pagosStats['street_total'];
            $this->_view->inHousePercent = $fogonTotal > 0
                ? round(($this->_view->inHousePaid / $fogonTotal) * 100)
                : 0;
            $this->_view->streetPercent = $streetTotal > 0
                ? round(($this->_view->streetPaid / $streetTotal) * 100)
                : 0;
            $this->_view->totalGenerated = $fogonTotal + $streetTotal;
            $this->_view->adelantosCount = $modelMovimientos->getAdelantosCount((int)$lastEmision->anio, (int)$lastEmision->mes);
        } else {
            $this->_view->inHousePaid = 0;
            $this->_view->streetPaid = 0;
            $this->_view->inHousePercent = 0;
            $this->_view->streetPercent = 0;
            $this->_view->totalGenerated = 0;
            $this->_view->adelantosCount = 0;
        }

        $atrasados = $modelSocios->getAtrasados();
        $this->_view->lateCount = count($atrasados);

        $this->_view->recentPayments = [];
        $this->_view->renderizar('index');
    }
}