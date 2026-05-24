-- ============================================================
-- Eventos & Decoraciones — Seed data
-- Run AFTER schema.sql: psql $DATABASE_URL -f database/seed.sql
-- ============================================================

-- Venues (Haciendas)
INSERT INTO venues (slug, name, description, base_price, base_guests, location_label, address, is_recommended, is_active) VALUES
('paz-del-rio',   'Hacienda Paz del Río',  'Hacienda colonial rodeada de montañas y jardines naturales, ideal para bodas íntimas con vista al río.',         4500000, 200, 'Boyacá, Colombia',          'Vereda Paz del Río, Boyacá',              TRUE,  TRUE),
('el-cedro',      'Hacienda El Cedro',     'Amplia hacienda cafetera con arquitectura tradicional y jardines en flor perfectos para tu día especial.',       3800000, 150, 'Cundinamarca, Colombia',     'Km 12 vía La Mesa, Cundinamarca',         FALSE, TRUE),
('san-juan',      'Hacienda San Juan',     'Imponente hacienda con salones para grandes celebraciones y vistas panorámicas al valle antioqueño.',            5200000, 250, 'Antioquia, Colombia',        'Vereda San Juan, Rionegro, Antioquia',    FALSE, TRUE),
('las-marias',    'Hacienda Las Marías',   'Acogedora finca de estilo campestre con piscina, zonas verdes y ambiente romántico para tu boda soñada.',        3200000, 120, 'Valle del Cauca, Colombia',  'Vereda Las Marías, Cali, Valle del Cauca',FALSE, TRUE),
('arkadia',       'Hacienda Arkadia',      'Lujosa hacienda moderna con instalaciones de primera clase y personalización total de cada detalle de tu evento.',6000000, 300, 'Bogotá D.C., Colombia',     'Autopista Norte km 18, Bogotá',           TRUE,  TRUE),
('botania',       'Hacienda Botania',      'Paraíso tropical con jardines botánicos únicos, cascadas naturales y ambiente mágico para bodas de ensueño.',    4000000, 180, 'Antioquia, Colombia',        'Vereda Botania, El Carmen de Viboral, Antioquia', FALSE, TRUE)
ON CONFLICT (slug) DO NOTHING;

-- Venue features (galería)
INSERT INTO venue_features (venue_id, title, image_url, display_order) VALUES
((SELECT id FROM venues WHERE slug='paz-del-rio'), 'Jardín Principal',   'assets/img/venues/paz-del-rio-garden.jpg',   1),
((SELECT id FROM venues WHERE slug='paz-del-rio'), 'Salón de Eventos',   'assets/img/venues/paz-del-rio-salon.jpg',    2),
((SELECT id FROM venues WHERE slug='paz-del-rio'), 'Vista al Río',       'assets/img/venues/paz-del-rio-river.jpg',    3),
((SELECT id FROM venues WHERE slug='arkadia'),     'Gran Salón',         'assets/img/venues/arkadia-salon.jpg',         1),
((SELECT id FROM venues WHERE slug='arkadia'),     'Terraza Panorámica', 'assets/img/venues/arkadia-terraza.jpg',       2),
((SELECT id FROM venues WHERE slug='arkadia'),     'Piscina y Zona VIP', 'assets/img/venues/arkadia-pool.jpg',          3),
((SELECT id FROM venues WHERE slug='botania'),     'Jardines Botánicos', 'assets/img/venues/botania-garden.jpg',        1),
((SELECT id FROM venues WHERE slug='botania'),     'Cascada Natural',    'assets/img/venues/botania-waterfall.jpg',     2),
((SELECT id FROM venues WHERE slug='botania'),     'Altar al Aire Libre','assets/img/venues/botania-altar.jpg',         3)
ON CONFLICT DO NOTHING;

-- Venue includes
INSERT INTO venue_includes (venue_id, description, display_order) VALUES
((SELECT id FROM venues WHERE slug='paz-del-rio'), 'Uso exclusivo de todas las instalaciones por 12 horas', 1),
((SELECT id FROM venues WHERE slug='paz-del-rio'), 'Parqueadero para 80 vehículos',                         2),
((SELECT id FROM venues WHERE slug='paz-del-rio'), 'Coordinador de eventos incluido',                       3),
((SELECT id FROM venues WHERE slug='paz-del-rio'), 'Zona de glamping para la noche de bodas',               4),
((SELECT id FROM venues WHERE slug='arkadia'),     'Uso exclusivo por 16 horas',                            1),
((SELECT id FROM venues WHERE slug='arkadia'),     'Parqueadero privado con valet parking',                 2),
((SELECT id FROM venues WHERE slug='arkadia'),     'Equipo de coordinación profesional',                    3),
((SELECT id FROM venues WHERE slug='arkadia'),     'Suite nupcial por una noche',                           4),
((SELECT id FROM venues WHERE slug='arkadia'),     'Iluminación arquitectónica incluida',                   5),
((SELECT id FROM venues WHERE slug='botania'),     'Uso exclusivo por 14 horas',                            1),
((SELECT id FROM venues WHERE slug='botania'),     'Recorrido guiado por los jardines',                     2),
((SELECT id FROM venues WHERE slug='botania'),     'Carpa de respaldo para lluvia',                         3)
ON CONFLICT DO NOTHING;

-- Services — Ceremony
INSERT INTO services (slug, category, name, price, price_unit, base_capacity, description, display_order) VALUES
('montaje',    'ceremony', 'Montaje Ceremonial',    1500000, 'fixed',      NULL, 'Decoración completa del espacio ceremonial con flores, telas y elementos temáticos según tu estilo.',          1),
('altar',      'ceremony', 'Altar Floral',          2000000, 'fixed',      NULL, 'Altar personalizado con arreglo floral elaborado a mano, marco fotográfico y alfombra de pétalos.',           2),
('sillas',     'ceremony', 'Sillas para Ceremonia',  800000, 'per_set',   50,   'Conjunto de 50 sillas tipo tiffany o chiavari para la ceremonia, con fajines y lazos incluidos.',              3),
('camino',     'ceremony', 'Camino de Pétalos',      600000, 'fixed',      NULL, 'Camino de pétalos naturales desde la entrada hasta el altar, con arreglos laterales de flores frescas.',      4)
ON CONFLICT (slug) DO NOTHING;

-- Services — Reception
INSERT INTO services (slug, category, name, price, price_unit, description, display_order) VALUES
('recepcion-clasica',    'reception', 'Recepción Clásica',    2500000, 'fixed', 'Decoración en tonos blancos y dorados con centros de mesa florales, mantelería de lino y candelabros.',     1),
('recepcion-elegante',   'reception', 'Recepción Elegante',   3500000, 'fixed', 'Ambiente de lujo con cristalería fina, florería importada, iluminación led ambiental y mesa sweetheart.',    2),
('recepcion-campestre',  'reception', 'Recepción Campestre',  2000000, 'fixed', 'Estética rústica con maderas naturales, flores silvestres, macetas y lámparas Edison colgantes.',           3),
('recepcion-playa',      'reception', 'Recepción Playa',      4000000, 'fixed', 'Ambientación tropical con conchas, palmas, telas ligeras y colores arena y turquesa para un mood veraniego.', 4)
ON CONFLICT (slug) DO NOTHING;

-- Services — Food
INSERT INTO services (slug, category, name, price, price_unit, description, display_order) VALUES
('menu-servido',       'food', 'Menú Servido',       85000, 'per_person', 'Tres tiempos servidos a la mesa: entrada, plato fuerte y postre, con opción de carne o pollo.',                1),
('menu-gourmet',       'food', 'Menú Gourmet',      120000, 'per_person', 'Experiencia gastronómica premium: cuatro tiempos con ingredientes de temporada preparados por chef invitado.',   2),
('menu-campestre',     'food', 'Menú Campestre',     65000, 'per_person', 'Buffet estilo campestre con asado, ensaladas frescas, arepas, yuca y chicharrón. ¡Sabor auténtico colombiano!',  3),
('pasabocas',          'food', 'Pasabocas',          45000, 'per_person', 'Selección de bocadillos fríos y calientes ideales para el cocktail de bienvenida y la recepción.',               4),
('menu-vegetariano',   'food', 'Menú Vegetariano',   75000, 'per_person', 'Menú 100% vegetariano con platos creativos, proteínas vegetales y presentación gourmet de tres tiempos.',        5),
('menu-infantil',      'food', 'Menú Infantil',      40000, 'per_person', 'Menú pensado para los niños: nuggets, mini hamburguesa, papas, jugo natural y postre sorpresa.',                  6)
ON CONFLICT (slug) DO NOTHING;

-- Services — Others
INSERT INTO services (slug, category, name, price, price_unit, description, display_order) VALUES
('decoracion-floral',  'others', 'Decoración Floral',          1800000, 'fixed', 'Paquete floral completo: bouquet de novia, boutonnieres, arreglos de mesa y decoración general del evento.', 1),
('detalles',           'others', 'Detalles Personalizados',     900000, 'fixed', 'Recuerdos y detalles personalizados para los invitados: tarjetas, velas, pequeños obsequios con sus iniciales.', 2),
('ambientacion',       'others', 'Ambientación Temática',      1200000, 'fixed', 'Transformación total del espacio con un tema elegido: boho, jardín encantado, vintage, moderno o tropical.',    3),
('sonido',             'others', 'Sonido y DJ',                2200000, 'fixed', 'Equipo de sonido profesional, DJ con repertorio personalizado, micrófono inalámbrico y pista de baile LED.',    4)
ON CONFLICT (slug) DO NOTHING;

-- Service features
INSERT INTO service_features (service_id, feature_text, display_order) VALUES
((SELECT id FROM services WHERE slug='montaje'),  'Decoración de sillas y arcos',         1),
((SELECT id FROM services WHERE slug='montaje'),  'Flores frescas de temporada',           2),
((SELECT id FROM services WHERE slug='montaje'),  'Montaje y desmontaje incluido',         3),
((SELECT id FROM services WHERE slug='altar'),    'Diseño personalizado',                  1),
((SELECT id FROM services WHERE slug='altar'),    'Pétalos naturales',                     2),
((SELECT id FROM services WHERE slug='altar'),    'Fotografía del altar incluida',         3),
((SELECT id FROM services WHERE slug='sonido'),   'Equipo profesional 5000W',              1),
((SELECT id FROM services WHERE slug='sonido'),   'DJ con playlist personalizada',         2),
((SELECT id FROM services WHERE slug='sonido'),   'Sistema de luces led',                  3),
((SELECT id FROM services WHERE slug='sonido'),   'Micrófono inalámbrico',                 4)
ON CONFLICT DO NOTHING;

-- Weddings (portfolio)
INSERT INTO weddings (bride_name, groom_name, wedding_date, venue_id, review_text, is_featured, display_order) VALUES
('Juana',   'Carlos',  '2024-03-15', (SELECT id FROM venues WHERE slug='paz-del-rio'), 'Fue el día más mágico de nuestras vidas. Cada detalle estuvo perfecto gracias al equipo de E&D.', TRUE,  1),
('Vanessa', 'Pedro',   '2024-06-22', (SELECT id FROM venues WHERE slug='el-cedro'),    'El equipo lo organizó todo a la perfección. Los invitados quedaron encantados con la hacienda.',   FALSE, 2),
('Tatiana', 'Felipe',  '2024-09-07', (SELECT id FROM venues WHERE slug='arkadia'),     'Soñé con una boda así desde niña. E&D lo hicieron realidad superando todas nuestras expectativas.', FALSE, 3),
('Laura',   'Jorge',   '2024-11-30', (SELECT id FROM venues WHERE slug='botania'),     'Los jardines de Botania son un sueño. Nuestros invitados no paran de hablar de lo hermoso que fue.',FALSE, 4)
ON CONFLICT DO NOTHING;

-- Wedding features
INSERT INTO wedding_features (wedding_id, title, description, display_order) VALUES
((SELECT id FROM weddings WHERE bride_name='Juana' AND groom_name='Carlos'), 'Ceremonia al Atardecer', 'Una ceremonia íntima a orillas del río con altar floral y camino de pétalos.', 1),
((SELECT id FROM weddings WHERE bride_name='Juana' AND groom_name='Carlos'), 'Recepción Clásica',      'Salón decorado en blanco y dorado con cena de tres tiempos y pista de baile.',    2),
((SELECT id FROM weddings WHERE bride_name='Tatiana' AND groom_name='Felipe'),'Gran Salón Arkadia',    'Celebración para 280 invitados con decoración elegante y menú gourmet.',           1),
((SELECT id FROM weddings WHERE bride_name='Tatiana' AND groom_name='Felipe'),'DJ y Show en Vivo',     'Noche de fiesta con DJ profesional y show de luces LED hasta el amanecer.',         2)
ON CONFLICT DO NOTHING;
