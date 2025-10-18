<?php
require_once 'auth.php';
require_once 'config.php';

// Redirigir al login si no está autenticado y se intenta acceder a funciones admin
if (isset($_GET['admin']) && !isAdminLoggedIn()) {
    header('Location: admin_login.php');
    exit;
}

// Cargar datos de servicios
$servicesData = json_decode(file_get_contents('services_data.json'), true);

// Cargar datos del portafolio
$portfolioData = json_decode(file_get_contents('portfolio_data.json'), true);

// Aplicar valores por defecto si están vacíos
if (empty($portfolioData['description'])) {
    $portfolioData['description'] = file_get_contents('assets/img/default/default.txt');
}

if (empty($portfolioData['images'])) {
    // Usar 6 copias de la imagen por defecto
    $portfolioData['images'] = array_fill(0, 6, 'assets/img/default/default.webp');
}
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Romero Odontología</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon/romero.ico">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,700&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/bss-overrides.css">
    <link rel="stylesheet" href="assets/css/untitled.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.css">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
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
    <section id="about" class="text-white bg-primary mb-0">
        <section class="py-4 py-xl-5">
            <div class="container position-relative">
                <h2 class="text-uppercase text-center text-white">Portafolio</h2>
                <hr class="mb-5 star-light" />
                <?php if (isAdminLoggedIn()): ?>
                    <div class="position-absolute" style="top: 0; right: 15px;">
                        <button class="btn btn-warning btn-sm" onclick="editPortfolio()">Modificar</button>
                    </div>
                <?php endif; ?>
                <div class="text-center mt-4"></div>
            </div>
            <div class="container">
                <div class="row gx-2 gy-2 row-cols-1 row-cols-md-2 row-cols-xl-3" data-bss-baguettebox id="portfolioGallery">
                    <?php foreach ($portfolioData['images'] as $index => $image): ?>
                        <div class="col" id="portfolio-img-<?php echo $index; ?>">
                            <a href="<?php echo htmlspecialchars($image); ?>" style="display: block; position: relative; width: 100%; padding-bottom: 66.67%; overflow: hidden; border-radius: 8px;">
                                <img src="<?php echo htmlspecialchars($image); ?>" style="position: absolute; top: 50%; left: 50%; width: 100%; height: 100%; object-fit: cover; object-position: center; transform: translate(-50%, -50%);" />
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <div style="font-size: 20px;text-align: center;max-width: 1024px;margin-left: auto;margin-right: auto;" id="portfolioDescription">
            <?php echo $portfolioData['description']; ?>
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
                <div class="col-lg-8 mx-auto">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d237.69686464668398!2d-96.9649573!3d19.1506606!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85c4dd88030bcd59%3A0x2835c5cd91e78046!2sEdificio%20GAMA!5e0!3m2!1ses!2smx!4v1234567890!5m2!1ses!2smx"
                        width="100%"
                        height="400"
                        style="border:0; border-radius: 8px;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>
    <footer class="text-center footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-5 mb-lg-0">
                    <h4 class="text-uppercase mb-4">Dirección</h4>
                    <p>Calle 2 Norte entre Avenidas 1 y 2&nbsp; #113 , <br>Edificio GAMA,&nbsp; Segundo Piso,&nbsp; Consultorio #9&nbsp;<br>Colonia Centro, C.P.&nbsp; 94100.<br>Huatusco, Veracruz, México</p>
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
                                <div class="mb-5 text-start"><?php echo $service['description']; ?></div>
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
                            <label class="form-label">Descripción del Servicio</label>
                            <div id="serviceDescriptionEditor" style="background-color: white; min-height: 150px;"></div>
                            <input type="hidden" name="serviceDescription" id="serviceDescriptionInput">
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

    <!-- Modal de Edición de Portafolio -->
    <div class="modal fade" id="editPortfolioModal" tabindex="-1" role="dialog" aria-labelledby="editPortfolioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPortfolioModalLabel">Editar Portafolio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPortfolioForm" method="POST" action="update_portfolio.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Galería de Imágenes -->
                        <div class="mb-4">
                            <label class="form-label"><strong>Imágenes de la Galería (6 imágenes)</strong></label>
                            <div class="row gx-2 gy-2" id="portfolioImagesEditor">
                                <?php foreach ($portfolioData['images'] as $index => $image): ?>
                                <div class="col-md-4" id="edit-img-<?php echo $index; ?>">
                                    <div class="card">
                                        <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top portfolio-preview-<?php echo $index; ?>" style="height: 150px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <input type="hidden" name="imageIndex[]" value="<?php echo $index; ?>">
                                            <input type="hidden" name="existingImages[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($image); ?>" class="existing-img-<?php echo $index; ?>">
                                            <input type="file" id="imageFile-<?php echo $index; ?>" name="imageFiles[<?php echo $index; ?>]" accept="image/*" style="display: none;" onchange="previewImageChange(<?php echo $index; ?>)">
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-warning btn-sm" onclick="document.getElementById('imageFile-<?php echo $index; ?>').click()">
                                                    <i class="fa fa-exchange"></i> Cambiar
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removePortfolioImage(<?php echo $index; ?>)">
                                                    <i class="fa fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Editor de Texto Rico -->
                        <div class="mb-3">
                            <label class="form-label"><strong>Descripción</strong></label>
                            <div id="portfolioDescriptionEditor" style="background-color: white; min-height: 200px;"></div>
                            <input type="hidden" name="portfolioDescription" id="portfolioDescriptionInput">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // Inicializar baguetteBox para la galería de portafolio
        baguetteBox.run('[data-bss-baguettebox]', {
            animation: 'slideIn',
            buttons: 'auto',
            fullScreen: false,
            noScrollbars: false,
            bodyClass: 'baguetteBox-open',
            titleTag: false,
            async: false,
            preload: 2,
            afterShow: null,
            afterHide: null,
            onChange: null,
            overlayBackgroundColor: 'rgba(0,0,0,.8)'
        });
    </script>

    <?php if (isAdminLoggedIn()): ?>
    <script>
        const servicesData = <?php echo file_get_contents('services_data.json'); ?>;
        let serviceQuill;

        function editService(serviceId) {
            const service = servicesData[serviceId];
            if (service) {
                // Llenar el formulario con los datos actuales
                document.getElementById('serviceId').value = serviceId;
                document.getElementById('serviceTitle').value = service.title;

                // Inicializar Quill editor para servicios si no existe
                if (!serviceQuill) {
                    serviceQuill = new Quill('#serviceDescriptionEditor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline'],
                                ['link'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['clean']
                            ]
                        }
                    });
                }

                // Cargar descripción en el editor
                serviceQuill.root.innerHTML = service.description || '';

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

        // Al enviar el formulario de servicio, guardar contenido del editor
        document.getElementById('editServiceForm').addEventListener('submit', function(e) {
            document.getElementById('serviceDescriptionInput').value = serviceQuill.root.innerHTML;
        });

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

        // Portafolio Management
        const portfolioData = <?php echo json_encode($portfolioData); ?>;
        let quill;
        let removedImages = [];

        function editPortfolio() {
            // Resetear imágenes eliminadas
            removedImages = [];

            // Inicializar Quill editor
            if (!quill) {
                quill = new Quill('#portfolioDescriptionEditor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['emoji'],
                            ['clean']
                        ]
                    }
                });
            }

            // Cargar contenido actual o dejar vacío si solo tiene HTML vacío
            const currentDescription = portfolioData.description;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = currentDescription;
            const textContent = tempDiv.textContent || tempDiv.innerText || '';

            if (textContent.trim().length > 0) {
                quill.root.innerHTML = currentDescription;
            } else {
                quill.setText(''); // Limpiar completamente el editor
            }

            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('editPortfolioModal'));
            modal.show();
        }

        function previewImageChange(index) {
            const fileInput = document.getElementById('imageFile-' + index);
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Actualizar preview
                    const preview = document.querySelector('.portfolio-preview-' + index);
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function removePortfolioImage(index) {
            // Restaurar imagen por defecto
            const preview = document.querySelector('.portfolio-preview-' + index);
            const existingInput = document.querySelector('.existing-img-' + index);
            const fileInput = document.getElementById('imageFile-' + index);

            preview.src = 'assets/img/default/default.webp';
            existingInput.value = 'assets/img/default/default.webp';
            fileInput.value = '';
        }

        // Al enviar el formulario
        document.getElementById('editPortfolioForm').addEventListener('submit', function(e) {
            // Guardar contenido del editor en campo oculto
            document.getElementById('portfolioDescriptionInput').value = quill.root.innerHTML;
        });
    </script>
    <?php endif; ?>
</body>

</html>