<?php

use App\Core\Session;

$usuario = Session::get('usuario');
$perfil = $usuario->perfil ?? 0;
$isAdmin = $perfil === 1;
$isCollector = $perfil === 3;
?>
<!Doctype html>
<html>

<head>
  <meta charset="UTF-8">
  <title><?php echo NOMBRE ?></title>
  <?php echo csrf_meta(); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo BASE_URL . 'src/output.css' ?>">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            fogon: {
              50: '#f0f9ff',
              100: '#e0f2fe',
              500: '#0ea5e9',
              800: '#1e3a5f',
              900: '#0c2340'
            }
          }
        }
      }
    }
  </script>
  <meta name=" viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <?php echo csrf_js(); ?>
</head>

<body class="bg-gray-100 font-sans antialiased">

  <div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-fogon-900 text-white flex-shrink-0 flex flex-col">

      <!-- Logo -->
      <div class="p-4 border-b border-fogon-800">
        <div class="flex items-center space-x-3">
          <img src="<?php echo BASE_URL . 'public/img/logo.min.svg' ?>" class="w-10 h-10" alt="Logo">
          <div>
            <h1 class="font-bold text-lg leading-tight">EL FOGÓN</h1>
            <p class="text-xs text-fogon-300">Club Deportivo y Social</p>
          </div>
        </div>
      </div>

      <!-- User Info -->
      <div class="p-4 border-b border-fogon-800">
        <p class="text-sm text-fogon-200">Bienvenid@</p>
        <p class="font-semibold"><?php echo $usuario->nombre ?? 'Usuario'; ?></p>
        <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full <?php echo $isAdmin ? 'bg-blue-500' : ($isCollector ? 'bg-amber-500' : 'bg-green-500'); ?>">
          <?php echo $isAdmin ? 'Administrador' : ($isCollector ? 'Cobrador' : 'Operador'); ?>
        </span>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-auto py-4">

        <!-- Operaciones -->
        <div class="px-4 py-2">
          <h3 class="text-xs font-semibold text-fogon-400 uppercase tracking-wider mb-2">Operaciones</h3>
          <ul class="space-y-1">
            <li>
              <a href="<?php echo BASE_URL ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition <?php echo $this->_route ?? '' === 'dashboard' ? 'bg-fogon-800' : '' ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
              </a>
            </li>
            <li>
              <a href="<?php echo BASE_URL . 'socios/listar' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Directorio de Socios
              </a>
            </li>
          </ul>
        </div>

        <!-- Cobranza -->
        <div class="px-4 py-2 mt-4">
          <h3 class="text-xs font-semibold text-fogon-400 uppercase tracking-wider mb-2">Cobranza</h3>
          <ul class="space-y-1">
            <li>
              <a href="<?php echo BASE_URL . 'movimientos/pagar' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Pagos en el Club
              </a>
            </li>
            <li>
              <a href="<?php echo BASE_URL . 'movimientos/generar' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                </svg>
                Generar Mes
              </a>
            </li>
            <li>
              <a href="<?php echo BASE_URL . 'movimientos/preprint' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Impresión Recibos
              </a>
            </li>
            <li>
              <a href="<?php echo BASE_URL . 'movimientos/adelantos' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pagos Adelantados
              </a>
            </li>
          </ul>
        </div>

        <!-- Admin -->
        <?php if ($isAdmin): ?>
          <div class="px-4 py-2 mt-4">
            <h3 class="text-xs font-semibold text-fogon-400 uppercase tracking-wider mb-2">Administración</h3>
            <ul class="space-y-1">
              <li>
                <a href="<?php echo BASE_URL . 'movimientos/totales' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition">
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                  Totales
                </a>
              </li>
              <li>
                <a href="<?php echo BASE_URL . 'movimientos/choose' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition">
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                  Impresión Seleccionados
                </a>
              </li>
              <li>
                <a href="<?php echo BASE_URL . 'movimientos/categorias' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition">
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                  </svg>
                  Categorías
                </a>
              </li>
              <li>
                <a href="<?php echo BASE_URL . 'ajustes/getajustes' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition">
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  Ajustes de Impresión
                </a>
              </li>
            </ul>
          </div>
        <?php endif; ?>

      </nav>

      <!-- Logout -->
      <div class="p-4 border-t border-fogon-800">
        <a href="<?php echo BASE_URL . 'usuarios/cambiar' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-fogon-800 transition mb-2">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Cambiar Contraseña
        </a>
        <a href="<?php echo BASE_URL . 'usuarios/logout' ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-red-600 transition text-red-300 hover:text-white">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          Cerrar Sesión
        </a>
      </div>

    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">

      <!-- Top Bar -->
      <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl font-semibold text-gray-800"><?php echo $this->titulo ?? 'Dashboard'; ?></h2>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">
              <?php echo date('d/m/Y'); ?>
            </span>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <article id="contenido" class="p-6">