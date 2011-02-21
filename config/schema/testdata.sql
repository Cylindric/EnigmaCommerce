INSERT INTO `enigma3_categories` (`id`, `parent_id`, `lft`, `rght`, `name`, `tag`, `description`, `stockcodeprefix`, `status`, `created`, `modified`, `legacy_id`, `legacy_parent_id`) VALUES
(1, 0, 0, 19, 'Top level', 'catrootnode', 'System root node', '', 1, NOW(), NOW(), 0, 0),
(2, 1, 1, 6, 'CategoryA', 'cata', 'Category A', '', 1, NOW(), NOW(), 0, 0),
(3, 2, 2, 3, 'SubCategoryAA', 'subcataa', 'Category A Subcat A', '', 1, NOW(), NOW(), 0, 0),
(4, 2, 4, 5, 'SubCategoryAB', 'subcatab', 'Category A Subcat B', '', 1, NOW(), NOW(), 0, 0),
(5, 1, 7, 12, 'CategoryB', 'catb', 'Category B', '', 1, NOW(), NOW(), 0, 0),
(6, 5, 8, 9, 'SubCategoryBA', 'subcatba', 'Category B Subcat A', '', 1, NOW(), NOW(), 0, 0),
(7, 5, 10, 11, 'SubCategoryBB', 'subcatbb', 'Category B Subcat B', '', 1, NOW(), NOW(), 0, 0),
(8, 1, 13, 18, 'CategoryC', 'catc', 'Category C', '', 1, NOW(), NOW(), 0, 0),
(9, 8, 14, 15, 'SubCategoryCA', 'subcatca', 'Category C Subcat A', '', 1, NOW(), NOW(), 0, 0),
(10, 8, 16, 17, 'SubCategoryCB', 'subcatcb', 'Category C Subcat B', '', 1, NOW(), NOW(), 0, 0);

INSERT INTO `enigma3_groups` (`id`, `name`, `access_admin`, `created`, `modified`) VALUES
(1, 'Administrators', 1, NOW(), NOW()),
(2, 'Managers', 1, NOW(), NOW()),
(3, 'Users', 0, NOW(), NOW());

INSERT INTO `enigma3_units` (`id`, `name`, `pluralname`, `unit`, `parent_id`, `scalefactor`, `created`, `modified`) VALUES
(1, '-none-', '-none-', '-', 0, 1, NOW(), NOW()),
(2, 'gallons per hour', 'gallons per hour', 'gph', 0, 1, NOW(), NOW()),
(3, 'watt', 'watts', 'W', 0, 1, NOW(), NOW()),
(4, 'horsepower', 'horsepower', 'HP', 3, 746, NOW(), NOW()),
(5, 'metre', 'metres', 'm', 0, 1, NOW(), NOW()),
(6, 'millimetre', 'millimetres', 'mm', 5, 0.001, NOW(), NOW()),
(7, 'centimetre', 'centimetres', 'cm', 5, 0.01, NOW(), NOW()),
(8, 'inch', 'inches', 'in', 5, 0.0254, NOW(), NOW()),
(9, 'foot', 'feet', 'ft', 5, 0.3048, NOW(), NOW()),
(10, 'yard', 'yards', 'yd', 5, 0.9144, NOW(), NOW()),
(11, 'kilogramme', 'kilogrammes', 'kg', 0, 1, NOW(), NOW()),
(12, 'gramme', 'grammes', 'g', 11, 0.001, NOW(), NOW()),
(13, 'litre', 'litres', 'L', 0, 1, NOW(), NOW()),
(14, 'millilitre', 'millilitres', 'ml', 13, 0.001, NOW(), NOW()),
(15, 'gallon', 'gallons', 'gal', 13, 4.54609, NOW(), NOW());

INSERT INTO `enigma3_users` (`id`, `group_id`, `username`, `password`, `created`, `modified`) VALUES
(1, 1, 'admin', '4398dba1ead645748f472b806c0146dcc074f8a3', NOW(), NOW()),
(2, 2, 'manager', '7a2a8220c209fcc5ddde66a86582160bdbdbea40', NOW(), NOW()),
(3, 3, 'user', '2500a148dc37ad34be4dade96973e9d675442882', NOW(), NOW());
