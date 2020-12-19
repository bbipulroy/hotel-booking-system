<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Dokan Hisab Management</title>

	<link href="css/style.css" rel="stylesheet">
</head>
<body class="c-app">
	<div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">
		<div class="c-sidebar-brand d-lg-down-none">
			DokanHisab
		</div>
		
		<ul class="c-sidebar-nav">
			<li class="c-sidebar-nav-item">
				<a class="c-sidebar-nav-link" href="#">
					<svg class="c-sidebar-nav-icon">
						<use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
					</svg>
					Dashboard
				</a>
			</li>
			
			<li class="c-sidebar-nav-item c-sidebar-nav-dropdown">
				<a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
					<svg class="c-sidebar-nav-icon">
						<use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-puzzle"></use>
					</svg>
					Customers
				</a>
				<ul class="c-sidebar-nav-dropdown-items">
					<li class="c-sidebar-nav-item">
						<a class="c-sidebar-nav-link" href="#">
							<span class="c-sidebar-nav-icon"></span>
							Customer List
						</a>
					</li>

					<li class="c-sidebar-nav-item">
						<a class="c-sidebar-nav-link" href="#">
							<span class="c-sidebar-nav-icon"></span>
							Add Customer
						</a>
					</li>
				</ul>
			</li>

			<li class="c-sidebar-nav-item c-sidebar-nav-dropdown">
				<a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
					<svg class="c-sidebar-nav-icon">
						<use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-puzzle"></use>
					</svg>
					Transactions
				</a>
				<ul class="c-sidebar-nav-dropdown-items">
					<li class="c-sidebar-nav-item">
						<a class="c-sidebar-nav-link" href="#">
							<span class="c-sidebar-nav-icon"></span>
							Add Pay
						</a>
					</li>

					<li class="c-sidebar-nav-item">
						<a class="c-sidebar-nav-link" href="#">
							<span class="c-sidebar-nav-icon"></span>
							Add Due
						</a>
					</li>

					<li class="c-sidebar-nav-item">
						<a class="c-sidebar-nav-link" href="#">
							<span class="c-sidebar-nav-icon"></span>
							Transactions
						</a>
					</li>
				</ul>
			</li>
		</ul>

		<button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent" data-class="c-sidebar-minimized"></button>
	</div>

	<div class="c-wrapper c-fixed-components">
		<header class="c-header c-header-light c-header-fixed c-header-with-subheader">
			<button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
				<svg class="c-icon c-icon-lg">
					<use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
				</svg>
			</button>

			<a class="c-header-brand d-lg-none" href="#">
				<svg width="118" height="46" alt="CoreUI Logo">
					<use xlink:href="assets/brand/coreui.svg#full"></use>
				</svg>
			</a>

			<button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
				<svg class="c-icon c-icon-lg">
					<use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
				</svg>
			</button>

			<ul class="c-header-nav d-md-down-none">
				<li class="c-header-nav-item px-3"><a class="c-header-nav-link" href="#">Customers</a></li>
				<li class="c-header-nav-item px-3"><a class="c-header-nav-link" href="#">Transactions</a></li>
			</ul>

			<ul class="c-header-nav ml-auto mr-4">
				<li class="c-header-nav-item dropdown">
					<a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
						<div class="c-avatar">
							<img class="c-avatar-img" src="assets/img/avatars/6.jpg" alt="user@email.com">
						</div>
					</a>

					<div class="dropdown-menu dropdown-menu-right pt-0">
						<a class="dropdown-item" href="#">
							<svg class="c-icon mr-2">
								<use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-bell"></use>
							</svg>
							Change Password
						</a>
						
						<a class="dropdown-item" href="#">
							<svg class="c-icon mr-2">
								<use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
							</svg>
							Logout
						</a>
					</div>
				</li>
			</ul>
		</header>
		
		<div class="c-body">
			<main class="c-main">
				<div class="container-fluid">
					<div class="fade-in">
						<div class="row">
							<div class="col-md-2"></div>
							<div class="col-md-8">
								<div class="row">
									<div class="col-md-6 text-right" style="font-weight: bold;">
										Today's Paid:
									</div>
									<div class="col-md-6">
										1200
									</div>
									<div class="col-md-12" style="margin-top: 10px;"></div>

									<div class="col-md-6 text-right" style="font-weight: bold;">
										Today's Due:
									</div>
									<div class="col-md-6">
										1200
									</div>
									<div class="col-md-12" style="margin-top: 25px;"></div>

									<div class="col-md-6 text-right" style="font-weight: bold;">
										Total Paid:
									</div>
									<div class="col-md-6">
										1200
									</div>
									<div class="col-md-12" style="margin-top: 10px;"></div>

									<div class="col-md-6 text-right" style="font-weight: bold;">
										Total Due:
									</div>
									<div class="col-md-6">
										1200
									</div>
									<div class="col-md-12" style="margin-top: 25px;"></div>

									<div class="col-md-6 text-right" style="font-weight: bold;">
										Total Customers:
									</div>
									<div class="col-md-6">
										50
									</div>
								</div>
							</div>
							<div class="col-md-2"></div>
						</div>
					</div>
				</div>
			</main>
		</div>
	</div>
	
	<script src="js/coreui.bundle.min.js"></script>
	<script src="js/svgxuse.min.js"></script>
	<script src="js/coreui-utils.js"></script>
</body>
</html>
