<?php
require_once 'auth.php';

// Redirigir al login si no está autenticado y se intenta acceder a funciones admin
if (isset($_GET['admin']) && !isAdminLoggedIn()) {
    header('Location: admin_login.php');
    exit;
}

// Cargar datos de servicios
$servicesData = json_decode(file_get_contents('services_data.json'), true);
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Home - Brand</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,700&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/bss-overrides.css">
    <link rel="stylesheet" href="assets/css/untitled.css">
</head>

<body id="page-top" data-bs-spy="scroll" data-bs-target="#mainNav" data-bs-offset="72">
    <nav class="navbar navbar-expand-lg fixed-top bg-secondary text-uppercase navbar-light" id="mainNav">
        <div class="container"><button data-bs-toggle="collapse" data-bs-target="#navbarResponsive" class="navbar-toggler text-white bg-primary navbar-toggler-right text-uppercase rounded" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><i class="fa fa-bars"></i></button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link px-0 px-lg-3 py-3 rounded" href="#portfolio">Servicios</a></li>
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link px-0 px-lg-3 py-3 rounded" href="#about">informaCIÓN</a></li>
                    <li class="nav-item mx-0 mx-lg-1"><a class="nav-link px-0 px-lg-3 py-3 rounded" href="#contact">CONTACTO</a></li>
                    <?php if (isAdminLoggedIn()): ?>
                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link px-0 px-lg-3 py-3 rounded" href="logout.php">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link px-0 px-lg-3 py-3 rounded" href="admin_login.php">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <header class="d-flex justify-content-center align-items-center" style="background: url(&quot;assets/img/rect225.svg&quot;) top / cover;min-height: 800px;"><img class="img-fluid" src="assets/img/g2080.svg" style="padding-left: 10px;padding-right: 10px;"></header>

    <?php if (isAdminLoggedIn() && (isset($_SESSION['message']) || isset($_SESSION['error']))): ?>
    <div class="container mt-3">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <section id="portfolio" class="portfolio">
        <div class="container">
            <h2 class="text-uppercase text-center text-secondary">Servicios</h2>
            <hr class="mb-5 star-dark">
            <div class="row">
                <?php foreach ($servicesData as $id => $service): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="position-relative">
                        <a class="d-block mx-auto portfolio-item" href="#portfolio-modal-<?php echo $id; ?>" data-bs-toggle="modal">
                            <div class="position-absolute d-flex w-100 h-100 portfolio-item-caption">
                                <div class="text-center text-white w-100 my-auto portfolio-item-caption-content"><i class="fa fa-search-plus fa-3x"></i></div>
                            </div><img class="img-fluid" src="<?php echo htmlspecialchars($service['image']); ?>">
                        </a>
                        <?php if (isAdminLoggedIn()): ?>
                            <div class="position-absolute" style="bottom: 10px; left: 50%; transform: translateX(-50%); z-index: 10;">
                                <button class="btn btn-warning btn-sm" onclick="editService(<?php echo $id; ?>)">Modificar</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section class="text-white bg-primary mb-0" id="about">
        <div class="container">
            <h2 class="text-uppercase text-center text-white">About</h2>
            <hr class="mb-5 star-light">
            <div class="row">
                <div class="col-lg-4 ms-auto">
                    <p class="lead">Freelancer is a free bootstrap theme. The download includes the complete source files including HTML, CSS, and JavaScript as well as optional LESS stylesheets for easy customization.</p>
                </div>
                <div class="col-lg-4 me-auto">
                    <p class="lead">Whether you're a student looking to showcase your work, a professional looking to attract clients, or a graphic artist looking to share your projects, this template is the perfect starting point!</p>
                </div>
            </div>
            <div class="text-center mt-4"><a class="btn btn-outline-light btn-xl" role="button" href="#"><i class="fa fa-download me-2"></i><span>Download Now!</span></a></div>
        </div>
    </section>
    <section id="contact">
        <div class="container">
            <h2 class="text-uppercase text-center text-secondary mb-0">contactaMe</h2>
            <hr class="mb-5 star-dark">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <form id="contactForm" name="sentMessage" data-bss-recipient="aca665c14726c1982e21e51a41cc327f">
                        <div class="control-group">
                            <div class="mb-0 pb-2 form-floating controls"><input class="form-control" type="text" id="name" required="" placeholder="Name"><label class="form-label">Nombre</label><small class="form-text text-danger help-block"></small></div>
                        </div>
                        <div class="control-group">
                            <div class="mb-0 pb-2 form-floating controls"><input class="form-control" type="email" id="email" required="" placeholder="Email Address"><label class="form-label">Dirección de Email</label><small class="form-text text-danger help-block"></small></div>
                        </div>
                        <div class="control-group">
                            <div class="mb-0 pb-2 form-floating controls"><input class="form-control" type="tel" id="phone" required="" placeholder="Phone Number"><label class="form-label">Número de Teléfono</label><small class="form-text text-danger help-block"></small></div>
                        </div>
                        <div class="control-group">
                            <div class="mb-5 pb-2 form-floating controls"><textarea class="form-control" id="message" required="" placeholder="Message" style="height: 150px;"></textarea><label class="form-label">Mensaje</label><small class="form-text text-danger help-block"></small></div>
                        </div>
                        <div id="success"></div>
                        <div><button class="btn btn-primary btn-xl" id="sendMessageButton" type="submit">Enviar</button></div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section id="mapa">
        <div class="container">
            <h2 class="text-uppercase text-center text-secondary mb-0">Ubicación</h2>
            <hr class="mb-5 star-dark">
            <div class="row">
                <div class="col-lg-8 mx-auto"><iframe allowfullscreen="" frameborder="0" src="https://cdn.bootstrapstudio.io/placeholders/map.html" width="100%" height="400"></iframe></div>
            </div>
        </div>
    </section>
    <footer class="text-center footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-5 mb-lg-0">
                    <h4 class="text-uppercase mb-4">Dirección</h4>
                    <p>Calle 2 Norte entre Avenidas 1 y 2&nbsp; #113 , <br>Edificio GAMA,&nbsp; Segundo Piso,&nbsp; Consultorio #9&nbsp;<br>Colonia Centro, C.P.&nbsp; 94100.</p>
                </div>
                <div class="col-md-4 mb-5 mb-lg-0">
                    <h4 class="text-uppercase">Redes sociales</h4>
                    <ul class="list-inline">
                        <li class="list-inline-item"><a class="btn btn-outline-light text-center btn-social rounded-circle" role="button" href="https://www.facebook.com/profile.php?id=61559704964320&amp;mibextid=ZbWKwL" target="_blank"><i class="fa fa-facebook fa-fw"></i></a></li>
                        <li class="list-inline-item"><a class="btn btn-outline-light text-center btn-social rounded-circle" role="button" href="#"><i class="fa fa-google-plus fa-fw"></i></a></li>
                        <li class="list-inline-item"><a class="btn btn-outline-light text-center btn-social rounded-circle" role="button" href="#"><i class="fa fa-twitter fa-fw"></i></a></li>
                        <li class="list-inline-item"><a class="btn btn-outline-light text-center btn-social rounded-circle" role="button" href="#"><i class="fa fa-dribbble fa-fw"></i></a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4 class="text-uppercase mb-4">Telefono y correo electrónico</h4>
                    <p class="lead mb-0"><span>Teléfono: 228 498 8544<br>Correo Electrónico: romerodontologia@gmail.com&nbsp;</span></p>
                </div>
            </div>
        </div>
    </footer>
    <div class="text-center text-white py-4 copyright">
        <div class="container"><small>Copyright © Romero Odontología 2024</small></div>
    </div>
    <div class="position-fixed d-lg-none scroll-to-top rounded"><a class="text-center d-block rounded text-white" href="#page-top"><i class="fa fa-chevron-up"></i></a></div>
    <?php foreach ($servicesData as $id => $service): ?>
    <div class="modal text-center" role="dialog" tabindex="-1" id="portfolio-modal-<?php echo $id; ?>">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header"><button class="btn-close" type="button" aria-label="Close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="container text-center">
                        <div class="row">
                            <div class="col-lg-8 mx-auto">
                                <h2 class="text-uppercase text-secondary mb-0"><?php echo htmlspecialchars($service['title']); ?></h2>
                                <hr class="mb-5 star-dark"><img class="img-fluid mb-5" src="<?php echo htmlspecialchars($service['image']); ?>">
                                <p class="mb-5"><?php echo htmlspecialchars($service['description']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer pb-5"><a class="btn btn-primary btn-lg mx-auto rounded-pill portfolio-modal-dismiss" role="button" data-bs-dismiss="modal"><i class="fa fa-close"></i>&nbsp;Close Project</a></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Modal de Edición de Servicios -->
    <?php if (isAdminLoggedIn()): ?>
    <div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="editServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editServiceModalLabel">Editar Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editServiceForm" method="POST" action="update_service.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="serviceId" name="serviceId">

                        <div class="mb-3">
                            <label for="serviceTitle" class="form-label">Título del Servicio</label>
                            <input type="text" class="form-control" id="serviceTitle" name="serviceTitle" required>
                        </div>

                        <div class="mb-3">
                            <label for="serviceImage" class="form-label">Imagen del Servicio</label>
                            <input type="file" class="form-control" id="serviceImage" name="serviceImage" accept="image/*">
                            <small class="form-text text-muted">Deja vacío si no quieres cambiar la imagen</small>
                            <div id="currentImagePreview" class="mt-2"></div>
                        </div>

                        <div class="mb-3">
                            <label for="serviceDescription" class="form-label">Descripción del Servicio</label>
                            <textarea class="form-control" id="serviceDescription" name="serviceDescription" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/smart-forms.min.js"></script>
    <script src="assets/js/freelancer.js"></script>

    <?php if (isAdminLoggedIn()): ?>
    <script>
        const servicesData = <?php echo file_get_contents('services_data.json'); ?>;

        function editService(serviceId) {
            const service = servicesData[serviceId];
            if (service) {
                // Llenar el formulario con los datos actuales
                document.getElementById('serviceId').value = serviceId;
                document.getElementById('serviceTitle').value = service.title;
                document.getElementById('serviceDescription').value = service.description;

                // Mostrar imagen actual
                const imagePreview = document.getElementById('currentImagePreview');
                imagePreview.innerHTML = `
                    <p><strong>Imagen actual:</strong></p>
                    <img src="${service.image}" alt="Imagen actual" style="max-width: 200px; height: auto;" class="img-thumbnail">
                `;

                // Abrir el modal
                const modal = new bootstrap.Modal(document.getElementById('editServiceModal'));
                modal.show();
            }
        }

        // Preview de la nueva imagen seleccionada
        document.getElementById('serviceImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imagePreview = document.getElementById('currentImagePreview');
                    imagePreview.innerHTML = `
                        <p><strong>Nueva imagen:</strong></p>
                        <img src="${e.target.result}" alt="Nueva imagen" style="max-width: 200px; height: auto;" class="img-thumbnail">
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    <?php endif; ?>
</body>

</html>