UPDATE `store_config` SET ConfigValue='http://localhost' WHERE ConfigKey='httphost';
UPDATE `store_config` SET ConfigValue='http' WHERE ConfigKey='httpsprotocol';
UPDATE `store_config` SET ConfigValue='1' WHERE ConfigKey='showerrors';
UPDATE `store_config` SET ConfigValue='0' WHERE ConfigKey='usessl';
UPDATE `store_config` SET ConfigValue='Enigma' WHERE ConfigKey='pagetitle';
UPDATE `store_config` SET ConfigValue='Enigma Corporation Limited' WHERE ConfigKey='companyname';
UPDATE `store_config` SET ConfigValue='EnigmaCorp' WHERE ConfigKey='shortcompanyname';
UPDATE `store_config` SET ConfigValue=20.00 WHERE ConfigKey='vatrate';

-- UPDATE `store_customer`
UPDATE `store_customer` SET
    FirstName = MD5(FirstName),
    LastName = MD5(LastName),
    InvAddress1 = MD5(InvAddress1),
    InvAddress2 = MD5(InvAddress2),
    InvAddress3 = MD5(InvAddress3),
    InvTown = MD5(InvTown),
    InvCounty = MD5(InvCounty),
    InvPostcode = MD5(InvPostcode),
    InvCountry = MD5(InvCountry),
    DelAddress1 = MD5(DelAddress1),
    DelAddress2 = MD5(DelAddress2),
    DelAddress3 = MD5(DelAddress3),
    DelTown = MD5(DelTown),
    DelCounty = MD5(DelCounty),
    DelPostcode = MD5(DelPostcode),
    DelCountry = MD5(DelCountry),
    Email = MD5(Email),
    DayPhone = MD5(DayPhone),
    ccInfoAvailable = -1,
    ccType = '',
    ccName = '',
    ccNo = '',
    ccExpMonth = '',
    ccExpYear = '',
    ccIssMonth = '',
    ccIssYear = '',
    ccIssue = '',
    ccCode = '';

-- DETAILS
-- Randomise RetailPrice by +/- 20%
-- Set WebPrice to 80% of Retail
-- Set RRP to 50% over retail
UPDATE `store_detail` SET RetailPrice = RetailPrice * (0.8+(rand()*0.4));
UPDATE `store_detail` SET WebPrice = RetailPrice * (0.8);
UPDATE `store_detail` SET RecommendedPrice = RetailPrice * (1.5) WHERE RecommendedPrice > 0;
UPDATE `store_detail` SET ExternalId = 0;

-- DETAIL SUPPLIER
-- Randomise Price by +/- 20%
UPDATE `store_detailsupplier` SET Price = Price * (0.8+(rand()*0.4));

-- HISTORY
-- Keep no history.
TRUNCATE TABLE `store_history`;
ALTER TABLE `store_history` AUTO_INCREMENT = 1;

-- ITEM
UPDATE `store_item` SET ExternalId = 0;

-- ORDER MESSAGES
-- Keep no messages
TRUNCATE TABLE `store_ordermessage`;
ALTER TABLE `store_ordermessage` AUTO_INCREMENT = 1;

-- SUPPLIERS
UPDATE `store_supplier` SET
    SupplierName = LEFT(MD5(SupplierName), FLOOR(6+RAND()*6)),
    Description = '',
    ExternalId = 0;

-- USERS
UPDATE `store_user` SET
    UserName='admin',
    FirstName='Admin',
    LastName='Admin',
    Password=MD5('admin')
WHERE UserID=1;
DELETE FROM `store_user` WHERE UserID>1;
ALTER TABLE `store_user` AUTO_INCREMENT = 2;
