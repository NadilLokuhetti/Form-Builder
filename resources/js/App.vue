<template>
    <div id="app">
        <nav class="navbar navbar-expand navbar-dark mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <i class="fas fa-window-restore me-2"></i>
                    <strong>Form Builder</strong>
                </a>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> Admin User
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <router-link to="/dashboard" class="nav-link" active-class="active">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Dashboard
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/forms" class="nav-link" active-class="active">
                                    <i class="fas fa-list-alt me-2"></i>
                                    Forms
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/submissions" class="nav-link" active-class="active">
                                    <i class="fas fa-list me-2"></i>
                                    Submissions
                                </router-link>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    <router-view></router-view>
                </main>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'App'
}
</script>

<style>
/* Your custom CSS can go here, or keep it in the external file */
</style>