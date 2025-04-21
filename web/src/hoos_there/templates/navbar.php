<!-- Navbar -->
<header>
  <nav class="navbar navbar-expand-md navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="?command=home">Hoo's There</a>
      <!-- Navbar Collapse -->
      <button 
        class="navbar-toggler" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#navbarNav" 
        aria-controls="navbarNav" 
        aria-expanded="false" 
        aria-label="Toggle navigation"
        >
      <span class="navbar-toggler-icon"></span>
      </button>
      <!-- Navbar Links -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="?command=academics">Academics</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="?command=social">Social & Professional Life</a>
          </li>
          <?php
            if ($this->isLoggedIn()) {
              $name = $this->getUserInfo()["name"];
            } else {
              $name = "Profile";
            }
          ?>
          <li class="nav-item">
            <a class="nav-link" href="?command=profile"><?=$name?></a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>
<!-- Navbar End -->
