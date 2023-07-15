<nav class="container navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">
        <h2 class="app-title ">Resume Assessment</h2>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <!-- <li class="nav-item <?php echo ($_SERVER['PHP_SELF'] == '/index.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php">Home</a>
            </li> -->
            <li class="nav-item <?php echo ($_SERVER['PHP_SELF'] == '/bulk-import.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="bulk-import.php">Bulk Upload</a>
            </li>
            <li class="nav-item <?php echo ($_SERVER['PHP_SELF'] == '/resume-list.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="resume-list.php">Resumes</a>
            </li>
            <li class="nav-item <?php echo ($_SERVER['PHP_SELF'] == '/job-roles.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="job-roles.php">Job Roles</a>
            </li>
            <?php if(!isset($_SESSION['login'])) { ?>
                <li class="nav-item <?php echo ($_SERVER['PHP_SELF'] == '/login.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
            <?php } else { ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            <?php } ?>
        </ul>
    </div>
</nav>
