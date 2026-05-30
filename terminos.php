<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones | CartonPets</title>
    <link rel="icon" type="image/png" href="assets/img/logocarton.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
</head>
<body class="d-flex flex-column min-height-100vh" style="background-color: var(--verde-suave);">
    
    <div class="top-bar">
        <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
            <a href="index.php"><img src="assets/img/logocarton.png" class="logo-circular-responsive"></a>
            <div class="user-utilities">
                <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">VOLVER AL HOME</a>
            </div>
        </div>
    </div>

    <main class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm text-dark">
                    <h2 class="fw-bold mb-2 text-center" style="color: var(--cafe-logo);">Términos y Condiciones de Venta</h2>
                    <p class="text-muted small text-center mb-5">Última actualización: Mayo 2026</p>
                    
                    <section class="mb-4">
                        <h5 class="fw-bold" style="color: var(--cafe-logo);">ASPECTOS GENERALES</h5>
                        <p class="text-secondary" style="line-height: 1.6;">El acceso, uso y compras realizadas en el sitio web www.cartonpets.cl se rigen estrictamente
                             por los presentes Términos y Condiciones, conforme a las leyes de la República de Chile, en particular la Ley N° 19.496 sobre
                              Protección de los Derechos de los Consumidores y la Ley N° 19.628 sobre Protección de la Vida Privada.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" style="color: var(--cafe-logo);">PROCESO DE COMPRA Y MEDIOS DE PAGO (WEBPAY)</h5>
                        <p class="text-secondary" style="line-height: 1.6;">Las transacciones comerciales dentro del sitio web se procesan de forma 
                            automatizada y segura a través de la pasarela de pagos Webpay de Transbank, permitiendo el uso de tarjetas de crédito, 
                            débito (Redcompra) y prepago emitidas por instituciones financieras chilenas. La orden de compra se considerará aprobada
                             únicamente cuando el sistema reciba la confirmación de pago exitosa por parte de Transbank. Una vez procesado el pago, 
                             el usuario recibirá un correo electrónico de respaldo con el detalle de su pedido y el comprobante de la transacción.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" style="color: var(--cafe-logo);">COBERTURA LOGÍSTICA Y ENTREGA PRIORITARIA</h5>
                        <p class="text-secondary" style="line-height: 1.6;">Debido a la naturaleza y urgencia que rodea el descanso final
                             de una mascota, CartonPets opera con un modelo logístico prioritario concentrado en la ciudad de Iquique, 
                             extendiendo la coordinación de despachos a comunas aledañas de la Región de Tarapacá (como Huara e interiores)
                              previo acuerdo directo. Las entregas o retiros físicos se gestionan con la máxima inmediatez posible y se
                               coordinan formalmente a través de nuestros canales oficiales de contacto (WhatsApp o Correo Electrónico).</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" style="color: var(--cafe-logo);">POLÍTICA DE DEVOLUCIONES POR PRODUCTO DAÑADO (GARANTÍA LEGAL)</h5>
                        <p class="text-secondary" style="line-height: 1.6;">En CartonPets nos comprometemos a entregar ataúdes ecológicos 
                            de cartón corrugado en perfectas condiciones estructurales y estéticas. En cumplimiento estricto de la 
                            legislación chilena, el cliente tiene el derecho y el deber de revisar el estado del producto al momento 
                            de la entrega o retiro físico. Si el ataúd presenta daños estructurales de fábrica evidentes, humedad severa 
                            o roturas en el material que comprometan su correcto uso ANTES de ser utilizado, el cliente podrá solicitar 
                            la aplicación de su garantía legal. Conforme a la ley, ante una falla de fábrica el consumidor podrá optar de
                             forma inmediata entre: (1) La sustitución directa y prioritaria del producto por uno idéntico en perfecto 
                             estado, o (2) La devolución total del dinero pagado. Para hacer efectiva esta garantía, el cliente debe 
                             comunicarse de urgencia a nuestro soporte oficial adjuntando el número de pedido y una fotografía que 
                             evidencie la falla del material. Al ser una situación de fuerza mayor, el caso será priorizado y resuelto 
                             en un plazo máximo de 24 horas hábiles.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" style="color: var(--cafe-logo);">DERECHO A RETRACTO</h5>
                        <p class="text-secondary" style="line-height: 1.6;">Dada la naturaleza e inmediatez del servicio, 
                            el cliente podrá solicitar la cancelación de la compra y la restitución total de los fondos siempre y 
                            cuando el producto NO haya salido a despacho o NO haya sido retirado de nuestras dependencias. 
                            Una vez entregado el producto en perfectas condiciones, y por motivos estrictamente sanitarios y éticos, 
                            no se admitirán devoluciones por mero retracto de compra.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" style="color: var(--cafe-logo);">DATOS PERSONALES Y SEGURIDAD</h5>
                        <p class="text-secondary" style="line-height: 1.6;">Los datos ingresados por los usuarios al momento de registrarse o 
                            realizar un pago serán tratados con absoluta confidencialidad y respeto. Serán utilizados única y exclusivamente para 
                            procesar el despacho del pedido, validar la transacción comercial en la base de datos interna y resguardar el historial 
                            del carrito de compras del usuario.</p>
                    </section>
                </div>
            </div>
        </div>
    </main>
    

    <footer class="py-5 mt-auto" style="background-color: var(--cafe-logo, #2d2a26) !important;">
        <div class="container text-center text-md-start px-lg-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <h6 class="fw-bold mb-3 text-white">NUESTRA MISIÓN</h6>
                    <p class="small text-white-50">Ofrecer una despedida que sea una declaración de amor por la naturaleza.</p>

                </div>
                <div class="col-md-4 text-center">
                    <h6 class="fw-bold mb-3 text-white">PROYECTO CARTONPETS</h6>
                    <p class="small text-white-50">Compromiso inquebrantable desde Iquique para todo Chile.</p>
                    <a class="nav-link text-white small fw-semibold py-1 px-2" href="terminos.php">Terminos y condiciones</a>
                </div>
                <div class="col-md-4 text-md-end">
                    <h6 class="fw-bold mb-3 text-white">CONTACTO</h6>
                    <p class="small text-white m-0">cartonpets@gmail.com</p>
                    <div class="mt-4 x-small text-white-50" style="font-size: 0.7rem;">&copy; 2026 JESUS LEON | INGENIERÍA EN INFORMÁTICA</div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>