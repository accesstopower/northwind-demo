<?php
// This script and data application were generated by AppGini 22.14
// Download AppGini for free from https://bigprof.com/appgini/download/

	/* Configuration */
	/*************************************/

		$pcConfig = [
			'customers' => [
			],
			'employees' => [
				'ReportsTo' => [
					'parent-table' => 'employees',
					'parent-primary-key' => 'EmployeeID',
					'child-primary-key' => 'EmployeeID',
					'child-primary-key-index' => 0,
					'tab-label' => 'Subordinates <span class="hidden child-label-employees child-field-caption">(Reports To)</span>',
					'auto-close' => false,
					'table-icon' => 'resources/table_icons/administrator.png',
					'display-refresh' => true,
					'display-add-new' => true,
					'forced-where' => '',
					'display-fields' => [2 => 'Photo', 3 => 'Last Name', 4 => 'First Name', 5 => 'Title', 7 => 'Age', 8 => 'Hire Date', 13 => 'Country', 17 => 'Reports To', 18 => 'Total Sales'],
					'display-field-names' => [2 => 'Photo', 3 => 'LastName', 4 => 'FirstName', 5 => 'Title', 7 => 'Age', 8 => 'HireDate', 13 => 'Country', 17 => 'ReportsTo', 18 => 'TotalSales'],
					'sortable-fields' => [0 => '`employees`.`EmployeeID`', 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => '`employees`.`BirthDate`', 7 => '`employees`.`Age`', 8 => '`employees`.`HireDate`', 9 => 10, 10 => 11, 11 => 12, 12 => 13, 13 => 14, 14 => 15, 15 => 16, 16 => 17, 17 => 18, 18 => '`employees`.`TotalSales`'],
					'records-per-page' => 10,
					'default-sort-by' => 3,
					'default-sort-direction' => 'asc',
					'open-detail-view-on-click' => true,
					'display-page-selector' => true,
					'show-page-progress' => true,
					'template' => 'children-employees',
					'template-printable' => 'children-employees-printable',
					'query' => "SELECT `employees`.`EmployeeID` as 'EmployeeID', `employees`.`TitleOfCourtesy` as 'TitleOfCourtesy', `employees`.`Photo` as 'Photo', `employees`.`LastName` as 'LastName', `employees`.`FirstName` as 'FirstName', `employees`.`Title` as 'Title', if(`employees`.`BirthDate`,date_format(`employees`.`BirthDate`,'%m/%d/%Y'),'') as 'BirthDate', `employees`.`Age` as 'Age', if(`employees`.`HireDate`,date_format(`employees`.`HireDate`,'%m/%d/%Y'),'') as 'HireDate', `employees`.`Address` as 'Address', `employees`.`City` as 'City', `employees`.`Region` as 'Region', `employees`.`PostalCode` as 'PostalCode', `employees`.`Country` as 'Country', `employees`.`HomePhone` as 'HomePhone', `employees`.`Extension` as 'Extension', `employees`.`Notes` as 'Notes', IF(    CHAR_LENGTH(`employees1`.`LastName`) || CHAR_LENGTH(`employees1`.`FirstName`), CONCAT_WS('',   `employees1`.`LastName`, ', ', `employees1`.`FirstName`), '') as 'ReportsTo', `employees`.`TotalSales` as 'TotalSales' FROM `employees` LEFT JOIN `employees` as employees1 ON `employees1`.`EmployeeID`=`employees`.`ReportsTo` "
				],
			],
			'orders' => [
				'CustomerID' => [
					'parent-table' => 'customers',
					'parent-primary-key' => 'CustomerID',
					'child-primary-key' => 'OrderID',
					'child-primary-key-index' => 0,
					'tab-label' => 'Customer\'s Orders <span class="hidden child-label-orders child-field-caption">(Customer)</span>',
					'auto-close' => false,
					'table-icon' => 'resources/table_icons/cash_register.png',
					'display-refresh' => true,
					'display-add-new' => true,
					'forced-where' => '',
					'display-fields' => [0 => 'Order ID', 1 => 'Status', 2 => 'Customer', 3 => 'Employee', 4 => 'Order Date', 7 => 'Ship Via', 14 => 'Ship Country', 15 => 'Total'],
					'display-field-names' => [0 => 'OrderID', 1 => 'status', 2 => 'CustomerID', 3 => 'EmployeeID', 4 => 'OrderDate', 7 => 'ShipVia', 14 => 'ShipCountry', 15 => 'total'],
					'sortable-fields' => [0 => '`orders`.`OrderID`', 1 => 2, 2 => '`customers1`.`CompanyName`', 3 => 4, 4 => '`orders`.`OrderDate`', 5 => '`orders`.`RequiredDate`', 6 => '`orders`.`ShippedDate`', 7 => '`shippers1`.`CompanyName`', 8 => '`orders`.`Freight`', 9 => '`customers1`.`CompanyName`', 10 => '`customers1`.`Address`', 11 => '`customers1`.`City`', 12 => '`customers1`.`Region`', 13 => '`customers1`.`PostalCode`', 14 => '`customers1`.`Country`', 15 => '`orders`.`total`'],
					'records-per-page' => 10,
					'default-sort-by' => 0,
					'default-sort-direction' => 'desc',
					'open-detail-view-on-click' => true,
					'display-page-selector' => true,
					'show-page-progress' => true,
					'template' => 'children-orders',
					'template-printable' => 'children-orders-printable',
					'query' => "SELECT `orders`.`OrderID` as 'OrderID', `orders`.`status` as 'status', IF(    CHAR_LENGTH(`customers1`.`CompanyName`), CONCAT_WS('',   `customers1`.`CompanyName`), '') as 'CustomerID', IF(    CHAR_LENGTH(`employees1`.`LastName`) || CHAR_LENGTH(`employees1`.`FirstName`), CONCAT_WS('',   `employees1`.`LastName`, ', ', `employees1`.`FirstName`), '') as 'EmployeeID', if(`orders`.`OrderDate`,date_format(`orders`.`OrderDate`,'%m/%d/%Y'),'') as 'OrderDate', if(`orders`.`RequiredDate`,date_format(`orders`.`RequiredDate`,'%m/%d/%Y'),'') as 'RequiredDate', if(`orders`.`ShippedDate`,date_format(`orders`.`ShippedDate`,'%m/%d/%Y'),'') as 'ShippedDate', IF(    CHAR_LENGTH(`shippers1`.`CompanyName`), CONCAT_WS('',   `shippers1`.`CompanyName`), '') as 'ShipVia', `orders`.`Freight` as 'Freight', IF(    CHAR_LENGTH(`customers1`.`CompanyName`), CONCAT_WS('',   `customers1`.`CompanyName`), '') as 'ShipName', IF(    CHAR_LENGTH(`customers1`.`Address`), CONCAT_WS('',   `customers1`.`Address`), '') as 'ShipAddress', IF(    CHAR_LENGTH(`customers1`.`City`), CONCAT_WS('',   `customers1`.`City`), '') as 'ShipCity', IF(    CHAR_LENGTH(`customers1`.`Region`), CONCAT_WS('',   `customers1`.`Region`), '') as 'ShipRegion', IF(    CHAR_LENGTH(`customers1`.`PostalCode`), CONCAT_WS('',   `customers1`.`PostalCode`), '') as 'ShipPostalCode', IF(    CHAR_LENGTH(`customers1`.`Country`), CONCAT_WS('',   `customers1`.`Country`), '') as 'ShipCountry', `orders`.`total` as 'total' FROM `orders` LEFT JOIN `customers` as customers1 ON `customers1`.`CustomerID`=`orders`.`CustomerID` LEFT JOIN `employees` as employees1 ON `employees1`.`EmployeeID`=`orders`.`EmployeeID` LEFT JOIN `shippers` as shippers1 ON `shippers1`.`ShipperID`=`orders`.`ShipVia` "
				],
				'EmployeeID' => [
					'parent-table' => 'employees',
					'parent-primary-key' => 'EmployeeID',
					'child-primary-key' => 'OrderID',
					'child-primary-key-index' => 0,
					'tab-label' => 'Initiated orders <span class="hidden child-label-orders child-field-caption">(Employee)</span>',
					'auto-close' => false,
					'table-icon' => 'resources/table_icons/cash_register.png',
					'display-refresh' => true,
					'display-add-new' => true,
					'forced-where' => '',
					'display-fields' => [0 => 'Order ID', 1 => 'Status', 2 => 'Customer', 3 => 'Employee', 4 => 'Order Date', 7 => 'Ship Via', 14 => 'Ship Country', 15 => 'Total'],
					'display-field-names' => [0 => 'OrderID', 1 => 'status', 2 => 'CustomerID', 3 => 'EmployeeID', 4 => 'OrderDate', 7 => 'ShipVia', 14 => 'ShipCountry', 15 => 'total'],
					'sortable-fields' => [0 => '`orders`.`OrderID`', 1 => 2, 2 => '`customers1`.`CompanyName`', 3 => 4, 4 => '`orders`.`OrderDate`', 5 => '`orders`.`RequiredDate`', 6 => '`orders`.`ShippedDate`', 7 => '`shippers1`.`CompanyName`', 8 => '`orders`.`Freight`', 9 => '`customers1`.`CompanyName`', 10 => '`customers1`.`Address`', 11 => '`customers1`.`City`', 12 => '`customers1`.`Region`', 13 => '`customers1`.`PostalCode`', 14 => '`customers1`.`Country`', 15 => '`orders`.`total`'],
					'records-per-page' => 10,
					'default-sort-by' => 0,
					'default-sort-direction' => 'desc',
					'open-detail-view-on-click' => true,
					'display-page-selector' => true,
					'show-page-progress' => true,
					'template' => 'children-orders',
					'template-printable' => 'children-orders-printable',
					'query' => "SELECT `orders`.`OrderID` as 'OrderID', `orders`.`status` as 'status', IF(    CHAR_LENGTH(`customers1`.`CompanyName`), CONCAT_WS('',   `customers1`.`CompanyName`), '') as 'CustomerID', IF(    CHAR_LENGTH(`employees1`.`LastName`) || CHAR_LENGTH(`employees1`.`FirstName`), CONCAT_WS('',   `employees1`.`LastName`, ', ', `employees1`.`FirstName`), '') as 'EmployeeID', if(`orders`.`OrderDate`,date_format(`orders`.`OrderDate`,'%m/%d/%Y'),'') as 'OrderDate', if(`orders`.`RequiredDate`,date_format(`orders`.`RequiredDate`,'%m/%d/%Y'),'') as 'RequiredDate', if(`orders`.`ShippedDate`,date_format(`orders`.`ShippedDate`,'%m/%d/%Y'),'') as 'ShippedDate', IF(    CHAR_LENGTH(`shippers1`.`CompanyName`), CONCAT_WS('',   `shippers1`.`CompanyName`), '') as 'ShipVia', `orders`.`Freight` as 'Freight', IF(    CHAR_LENGTH(`customers1`.`CompanyName`), CONCAT_WS('',   `customers1`.`CompanyName`), '') as 'ShipName', IF(    CHAR_LENGTH(`customers1`.`Address`), CONCAT_WS('',   `customers1`.`Address`), '') as 'ShipAddress', IF(    CHAR_LENGTH(`customers1`.`City`), CONCAT_WS('',   `customers1`.`City`), '') as 'ShipCity', IF(    CHAR_LENGTH(`customers1`.`Region`), CONCAT_WS('',   `customers1`.`Region`), '') as 'ShipRegion', IF(    CHAR_LENGTH(`customers1`.`PostalCode`), CONCAT_WS('',   `customers1`.`PostalCode`), '') as 'ShipPostalCode', IF(    CHAR_LENGTH(`customers1`.`Country`), CONCAT_WS('',   `customers1`.`Country`), '') as 'ShipCountry', `orders`.`total` as 'total' FROM `orders` LEFT JOIN `customers` as customers1 ON `customers1`.`CustomerID`=`orders`.`CustomerID` LEFT JOIN `employees` as employees1 ON `employees1`.`EmployeeID`=`orders`.`EmployeeID` LEFT JOIN `shippers` as shippers1 ON `shippers1`.`ShipperID`=`orders`.`ShipVia` "
				],
				'ShipVia' => [
					'parent-table' => 'shippers',
					'parent-primary-key' => 'ShipperID',
					'child-primary-key' => 'OrderID',
					'child-primary-key-index' => 0,
					'tab-label' => 'Orders via shipper <span class="hidden child-label-orders child-field-caption">(Ship Via)</span>',
					'auto-close' => false,
					'table-icon' => 'resources/table_icons/cash_register.png',
					'display-refresh' => true,
					'display-add-new' => true,
					'forced-where' => '',
					'display-fields' => [0 => 'Order ID', 1 => 'Status', 2 => 'Customer', 3 => 'Employee', 4 => 'Order Date', 7 => 'Ship Via', 14 => 'Ship Country', 15 => 'Total'],
					'display-field-names' => [0 => 'OrderID', 1 => 'status', 2 => 'CustomerID', 3 => 'EmployeeID', 4 => 'OrderDate', 7 => 'ShipVia', 14 => 'ShipCountry', 15 => 'total'],
					'sortable-fields' => [0 => '`orders`.`OrderID`', 1 => 2, 2 => '`customers1`.`CompanyName`', 3 => 4, 4 => '`orders`.`OrderDate`', 5 => '`orders`.`RequiredDate`', 6 => '`orders`.`ShippedDate`', 7 => '`shippers1`.`CompanyName`', 8 => '`orders`.`Freight`', 9 => '`customers1`.`CompanyName`', 10 => '`customers1`.`Address`', 11 => '`customers1`.`City`', 12 => '`customers1`.`Region`', 13 => '`customers1`.`PostalCode`', 14 => '`customers1`.`Country`', 15 => '`orders`.`total`'],
					'records-per-page' => 10,
					'default-sort-by' => 0,
					'default-sort-direction' => 'desc',
					'open-detail-view-on-click' => true,
					'display-page-selector' => true,
					'show-page-progress' => true,
					'template' => 'children-orders',
					'template-printable' => 'children-orders-printable',
					'query' => "SELECT `orders`.`OrderID` as 'OrderID', `orders`.`status` as 'status', IF(    CHAR_LENGTH(`customers1`.`CompanyName`), CONCAT_WS('',   `customers1`.`CompanyName`), '') as 'CustomerID', IF(    CHAR_LENGTH(`employees1`.`LastName`) || CHAR_LENGTH(`employees1`.`FirstName`), CONCAT_WS('',   `employees1`.`LastName`, ', ', `employees1`.`FirstName`), '') as 'EmployeeID', if(`orders`.`OrderDate`,date_format(`orders`.`OrderDate`,'%m/%d/%Y'),'') as 'OrderDate', if(`orders`.`RequiredDate`,date_format(`orders`.`RequiredDate`,'%m/%d/%Y'),'') as 'RequiredDate', if(`orders`.`ShippedDate`,date_format(`orders`.`ShippedDate`,'%m/%d/%Y'),'') as 'ShippedDate', IF(    CHAR_LENGTH(`shippers1`.`CompanyName`), CONCAT_WS('',   `shippers1`.`CompanyName`), '') as 'ShipVia', `orders`.`Freight` as 'Freight', IF(    CHAR_LENGTH(`customers1`.`CompanyName`), CONCAT_WS('',   `customers1`.`CompanyName`), '') as 'ShipName', IF(    CHAR_LENGTH(`customers1`.`Address`), CONCAT_WS('',   `customers1`.`Address`), '') as 'ShipAddress', IF(    CHAR_LENGTH(`customers1`.`City`), CONCAT_WS('',   `customers1`.`City`), '') as 'ShipCity', IF(    CHAR_LENGTH(`customers1`.`Region`), CONCAT_WS('',   `customers1`.`Region`), '') as 'ShipRegion', IF(    CHAR_LENGTH(`customers1`.`PostalCode`), CONCAT_WS('',   `customers1`.`PostalCode`), '') as 'ShipPostalCode', IF(    CHAR_LENGTH(`customers1`.`Country`), CONCAT_WS('',   `customers1`.`Country`), '') as 'ShipCountry', `orders`.`total` as 'total' FROM `orders` LEFT JOIN `customers` as customers1 ON `customers1`.`CustomerID`=`orders`.`CustomerID` LEFT JOIN `employees` as employees1 ON `employees1`.`EmployeeID`=`orders`.`EmployeeID` LEFT JOIN `shippers` as shippers1 ON `shippers1`.`ShipperID`=`orders`.`ShipVia` "
				],
			],
			'order_details' => [
				'OrderID' => [
					'parent-table' => 'orders',
					'parent-primary-key' => 'OrderID',
					'child-primary-key' => 'odID',
					'child-primary-key-index' => 0,
					'tab-label' => 'Order Items <span class="hidden child-label-order_details child-field-caption">(Order ID)</span>',
					'auto-close' => true,
					'table-icon' => 'resources/table_icons/application_form_magnify.png',
					'display-refresh' => true,
					'display-add-new' => true,
					'forced-where' => '',
					'display-fields' => [1 => 'Order ID', 2 => 'Product', 3 => 'Category', 6 => 'Unit Price', 7 => 'Quantity', 8 => 'Discount', 9 => 'Subtotal'],
					'display-field-names' => [1 => 'OrderID', 2 => 'ProductID', 3 => 'Category', 6 => 'UnitPrice', 7 => 'Quantity', 8 => 'Discount', 9 => 'Subtotal'],
					'sortable-fields' => [0 => '`order_details`.`odID`', 1 => '`orders1`.`OrderID`', 2 => '`products1`.`ProductName`', 3 => 4, 4 => '`products1`.`UnitPrice`', 5 => '`products1`.`UnitsInStock`', 6 => '`order_details`.`UnitPrice`', 7 => '`order_details`.`Quantity`', 8 => '`order_details`.`Discount`', 9 => '`order_details`.`Subtotal`'],
					'records-per-page' => 10,
					'default-sort-by' => 1,
					'default-sort-direction' => 'asc',
					'open-detail-view-on-click' => true,
					'display-page-selector' => true,
					'show-page-progress' => true,
					'template' => 'children-order_details',
					'template-printable' => 'children-order_details-printable',
					'query' => "SELECT `order_details`.`odID` as 'odID', IF(    CHAR_LENGTH(`orders1`.`OrderID`), CONCAT_WS('',   `orders1`.`OrderID`), '') as 'OrderID', IF(    CHAR_LENGTH(`products1`.`ProductName`), CONCAT_WS('',   `products1`.`ProductName`), '') as 'ProductID', IF(    CHAR_LENGTH(`categories1`.`CategoryName`) || CHAR_LENGTH(`suppliers1`.`CompanyName`), CONCAT_WS('',   `categories1`.`CategoryName`, ' / ', `suppliers1`.`CompanyName`), '') as 'Category', IF(    CHAR_LENGTH(`products1`.`UnitPrice`), CONCAT_WS('',   `products1`.`UnitPrice`), '') as 'CatalogPrice', IF(    CHAR_LENGTH(`products1`.`UnitsInStock`), CONCAT_WS('',   `products1`.`UnitsInStock`), '') as 'UnitsInStock', CONCAT('$', FORMAT(`order_details`.`UnitPrice`, 2)) as 'UnitPrice', `order_details`.`Quantity` as 'Quantity', CONCAT('$', FORMAT(`order_details`.`Discount`, 2)) as 'Discount', `order_details`.`Subtotal` as 'Subtotal' FROM `order_details` LEFT JOIN `orders` as orders1 ON `orders1`.`OrderID`=`order_details`.`OrderID` LEFT JOIN `products` as products1 ON `products1`.`ProductID`=`order_details`.`ProductID` LEFT JOIN `categories` as categories1 ON `categories1`.`CategoryID`=`products1`.`CategoryID` LEFT JOIN `suppliers` as suppliers1 ON `suppliers1`.`SupplierID`=`products1`.`SupplierID` "
				],
				'ProductID' => [
					'parent-table' => 'products',
					'parent-primary-key' => 'ProductID',
					'child-primary-key' => 'odID',
					'child-primary-key-index' => 0,
					'tab-label' => 'Orders for this product <span class="hidden child-label-order_details child-field-caption">(Product)</span>',
					'auto-close' => false,
					'table-icon' => 'resources/table_icons/application_form_magnify.png',
					'display-refresh' => true,
					'display-add-new' => true,
					'forced-where' => '',
					'display-fields' => [1 => 'Order ID', 2 => 'Product', 3 => 'Category', 6 => 'Unit Price', 7 => 'Quantity', 8 => 'Discount', 9 => 'Subtotal'],
					'display-field-names' => [1 => 'OrderID', 2 => 'ProductID', 3 => 'Category', 6 => 'UnitPrice', 7 => 'Quantity', 8 => 'Discount', 9 => 'Subtotal'],
					'sortable-fields' => [0 => '`order_details`.`odID`', 1 => '`orders1`.`OrderID`', 2 => '`products1`.`ProductName`', 3 => 4, 4 => '`products1`.`UnitPrice`', 5 => '`products1`.`UnitsInStock`', 6 => '`order_details`.`UnitPrice`', 7 => '`order_details`.`Quantity`', 8 => '`order_details`.`Discount`', 9 => '`order_details`.`Subtotal`'],
					'records-per-page' => 10,
					'default-sort-by' => 1,
					'default-sort-direction' => 'asc',
					'open-detail-view-on-click' => true,
					'display-page-selector' => true,
					'show-page-progress' => true,
					'template' => 'children-order_details',
					'template-printable' => 'children-order_details-printable',
					'query' => "SELECT `order_details`.`odID` as 'odID', IF(    CHAR_LENGTH(`orders1`.`OrderID`), CONCAT_WS('',   `orders1`.`OrderID`), '') as 'OrderID', IF(    CHAR_LENGTH(`products1`.`ProductName`), CONCAT_WS('',   `products1`.`ProductName`), '') as 'ProductID', IF(    CHAR_LENGTH(`categories1`.`CategoryName`) || CHAR_LENGTH(`suppliers1`.`CompanyName`), CONCAT_WS('',   `categories1`.`CategoryName`, ' / ', `suppliers1`.`CompanyName`), '') as 'Category', IF(    CHAR_LENGTH(`products1`.`UnitPrice`), CONCAT_WS('',   `products1`.`UnitPrice`), '') as 'CatalogPrice', IF(    CHAR_LENGTH(`products1`.`UnitsInStock`), CONCAT_WS('',   `products1`.`UnitsInStock`), '') as 'UnitsInStock', CONCAT('$', FORMAT(`order_details`.`UnitPrice`, 2)) as 'UnitPrice', `order_details`.`Quantity` as 'Quantity', CONCAT('$', FORMAT(`order_details`.`Discount`, 2)) as 'Discount', `order_details`.`Subtotal` as 'Subtotal' FROM `order_details` LEFT JOIN `orders` as orders1 ON `orders1`.`OrderID`=`order_details`.`OrderID` LEFT JOIN `products` as products1 ON `products1`.`ProductID`=`order_details`.`ProductID` LEFT JOIN `categories` as categories1 ON `categories1`.`CategoryID`=`products1`.`CategoryID` LEFT JOIN `suppliers` as suppliers1 ON `suppliers1`.`SupplierID`=`products1`.`SupplierID` "
				],
			],
			'products' => [
				'SupplierID' => [
					'parent-table' => 'suppliers',
					'parent-primary-key' => 'SupplierID',
					'child-primary-key' => 'ProductID',
					'child-primary-key-index' => 0,
					'tab-label' => 'Products supplied <span class="hidden child-label-products child-field-caption">(Supplier)</span>',
					'auto-close' => false,
					'table-icon' => 'resources/table_icons/handbag.png',
					'display-refresh' => true,
					'display-add-new' => true,
					'forced-where' => '',
					'display-fields' => [1 => 'Product Name', 2 => 'Supplier', 3 => 'Category', 4 => 'Quantity Per Unit', 5 => 'Unit Price', 9 => 'Discontinued'],
					'display-field-names' => [1 => 'ProductName', 2 => 'SupplierID', 3 => 'CategoryID', 4 => 'QuantityPerUnit', 5 => 'UnitPrice', 9 => 'Discontinued'],
					'sortable-fields' => [0 => '`products`.`ProductID`', 1 => 2, 2 => '`suppliers1`.`CompanyName`', 3 => '`categories1`.`CategoryName`', 4 => 5, 5 => '`products`.`UnitPrice`', 6 => '`products`.`UnitsInStock`', 7 => '`products`.`UnitsOnOrder`', 8 => '`products`.`ReorderLevel`', 9 => '`products`.`Discontinued`'],
					'records-per-page' => 10,
					'default-sort-by' => 1,
					'default-sort-direction' => 'asc',
					'open-detail-view-on-click' => true,
					'display-page-selector' => true,
					'show-page-progress' => true,
					'template' => 'children-products',
					'template-printable' => 'children-products-printable',
					'query' => "SELECT `products`.`ProductID` as 'ProductID', `products`.`ProductName` as 'ProductName', IF(    CHAR_LENGTH(`suppliers1`.`CompanyName`), CONCAT_WS('',   `suppliers1`.`CompanyName`), '') as 'SupplierID', IF(    CHAR_LENGTH(`categories1`.`CategoryName`), CONCAT_WS('',   `categories1`.`CategoryName`), '') as 'CategoryID', `products`.`QuantityPerUnit` as 'QuantityPerUnit', CONCAT('$', FORMAT(`products`.`UnitPrice`, 2)) as 'UnitPrice', `products`.`UnitsInStock` as 'UnitsInStock', `products`.`UnitsOnOrder` as 'UnitsOnOrder', `products`.`ReorderLevel` as 'ReorderLevel', concat('<i class=\"glyphicon glyphicon-', if(`products`.`Discontinued`, 'check', 'unchecked'), '\"></i>') as 'Discontinued' FROM `products` LEFT JOIN `suppliers` as suppliers1 ON `suppliers1`.`SupplierID`=`products`.`SupplierID` LEFT JOIN `categories` as categories1 ON `categories1`.`CategoryID`=`products`.`CategoryID` "
				],
				'CategoryID' => [
					'parent-table' => 'categories',
					'parent-primary-key' => 'CategoryID',
					'child-primary-key' => 'ProductID',
					'child-primary-key-index' => 0,
					'tab-label' => 'Products under this category <span class="hidden child-label-products child-field-caption">(Category)</span>',
					'auto-close' => false,
					'table-icon' => 'resources/table_icons/handbag.png',
					'display-refresh' => true,
					'display-add-new' => true,
					'forced-where' => '',
					'display-fields' => [1 => 'Product Name', 2 => 'Supplier', 3 => 'Category', 4 => 'Quantity Per Unit', 5 => 'Unit Price', 9 => 'Discontinued'],
					'display-field-names' => [1 => 'ProductName', 2 => 'SupplierID', 3 => 'CategoryID', 4 => 'QuantityPerUnit', 5 => 'UnitPrice', 9 => 'Discontinued'],
					'sortable-fields' => [0 => '`products`.`ProductID`', 1 => 2, 2 => '`suppliers1`.`CompanyName`', 3 => '`categories1`.`CategoryName`', 4 => 5, 5 => '`products`.`UnitPrice`', 6 => '`products`.`UnitsInStock`', 7 => '`products`.`UnitsOnOrder`', 8 => '`products`.`ReorderLevel`', 9 => '`products`.`Discontinued`'],
					'records-per-page' => 10,
					'default-sort-by' => 1,
					'default-sort-direction' => 'asc',
					'open-detail-view-on-click' => true,
					'display-page-selector' => true,
					'show-page-progress' => true,
					'template' => 'children-products',
					'template-printable' => 'children-products-printable',
					'query' => "SELECT `products`.`ProductID` as 'ProductID', `products`.`ProductName` as 'ProductName', IF(    CHAR_LENGTH(`suppliers1`.`CompanyName`), CONCAT_WS('',   `suppliers1`.`CompanyName`), '') as 'SupplierID', IF(    CHAR_LENGTH(`categories1`.`CategoryName`), CONCAT_WS('',   `categories1`.`CategoryName`), '') as 'CategoryID', `products`.`QuantityPerUnit` as 'QuantityPerUnit', CONCAT('$', FORMAT(`products`.`UnitPrice`, 2)) as 'UnitPrice', `products`.`UnitsInStock` as 'UnitsInStock', `products`.`UnitsOnOrder` as 'UnitsOnOrder', `products`.`ReorderLevel` as 'ReorderLevel', concat('<i class=\"glyphicon glyphicon-', if(`products`.`Discontinued`, 'check', 'unchecked'), '\"></i>') as 'Discontinued' FROM `products` LEFT JOIN `suppliers` as suppliers1 ON `suppliers1`.`SupplierID`=`products`.`SupplierID` LEFT JOIN `categories` as categories1 ON `categories1`.`CategoryID`=`products`.`CategoryID` "
				],
			],
			'categories' => [
			],
			'suppliers' => [
			],
			'shippers' => [
			],
		];

	/*************************************/
	/* End of configuration */


	include_once(__DIR__ . '/lib.php');
	@header('Content-Type: text/html; charset=' . datalist_db_encoding);

	handle_maintenance();

	/**
	* dynamic configuration based on current user's permissions
	* $userPCConfig array is populated only with parent tables where the user has access to
	* at least one child table
	*/
	$userPCConfig = [];
	foreach($pcConfig as $pcChildTable => $ChildrenLookups) {
		$permChild = getTablePermissions($pcChildTable);
		if(!$permChild['view']) continue;

		foreach($ChildrenLookups as $ChildLookupField => $ChildConfig) {
			$permParent = getTablePermissions($ChildConfig['parent-table']);
			if(!$permParent['view']) continue;

			$userPCConfig[$pcChildTable][$ChildLookupField] = $pcConfig[$pcChildTable][$ChildLookupField];
			// show add new only if configured above AND the user has insert permission
			$userPCConfig[$pcChildTable][$ChildLookupField]['display-add-new'] = ($permChild['insert'] && $pcConfig[$pcChildTable][$ChildLookupField]['display-add-new']);
		}
	}

	/* Receive, UTF-convert, and validate parameters */
	$ParentTable = Request::val('ParentTable'); // needed only with operation=show-children, will be validated in the processing code
	$ChildTable = Request::val('ChildTable');
		if(!in_array($ChildTable, array_keys($userPCConfig))) {
			/* defaults to first child table in config array if not provided */
			$ChildTable = current(array_keys($userPCConfig));
		}
		if(!$ChildTable) { die('<!-- No tables accessible to current user -->'); }
	$SelectedID = strip_tags(Request::val('SelectedID'));
	$ChildLookupField = Request::val('ChildLookupField');
		if(!in_array($ChildLookupField, array_keys($userPCConfig[$ChildTable]))) {
			/* defaults to first lookup in current child config array if not provided */
			$ChildLookupField = current(array_keys($userPCConfig[$ChildTable]));
		}

	if(function_exists('child_records_config')) {
		// $userPCConfig is passed by reference
		child_records_config($ChildTable, $ChildLookupField, $userPCConfig);
	}

	$currentConfig = $userPCConfig[$ChildTable][$ChildLookupField];
	if(empty($currentConfig))
		die('<!-- No tables accessible to current user -->');

	$Page = intval(Request::val('Page'));
		if($Page < 1) $Page = 1;
	$SortBy = (Request::val('SortBy') != '' ? abs(intval(Request::val('SortBy'))) : false);
		if(!in_array($SortBy, array_keys($currentConfig['sortable-fields']), true))
			$SortBy = $currentConfig['default-sort-by'];
	$SortDirection = strtolower(Request::val('SortDirection'));
		if(!in_array($SortDirection, ['asc', 'desc']))
			$SortDirection = $currentConfig['default-sort-direction'];
	$Operation = strtolower(Request::val('Operation'));
		if(!in_array($Operation, ['get-records', 'show-children', 'get-records-printable', 'show-children-printable']))
			$Operation = 'get-records';

	/* process requested operation */
	switch($Operation) {
		/************************************************/
		case 'show-children':
			/* populate HTML and JS content with children tabs */
			$tabLabels = $tabPanels = $tabLoaders = '';
			foreach($userPCConfig as $ChildTable => $childLookups) {
				foreach($childLookups as $ChildLookupField => $childConfig) {
					if($childConfig['parent-table'] != $ParentTable) continue;

					$TableIcon = ($childConfig['table-icon'] ? "<img src=\"{$childConfig['table-icon']}\" border=\"0\">" : '');

					$tabLabels .= "<li class=\"child-tab-label child-table-{$ChildTable} lookup-field-{$ChildLookupField} " . ($tabLabels ? '' : 'active') . "\">" .
							"<a href=\"#panel_{$ChildTable}-{$ChildLookupField}\" id=\"tab_{$ChildTable}-{$ChildLookupField}\" data-toggle=\"tab\">" .
								$TableIcon . $childConfig['tab-label'] .
								"<span class=\"badge child-count child-count-{$ChildTable}-{$ChildLookupField}\"></span>" .
							"</a>" .
						"</li>\n\t\t\t\t";

					$tabPanels .= "<div id=\"panel_{$ChildTable}-{$ChildLookupField}\" class=\"tab-pane" . ($tabPanels ? '' : ' active') . "\">" .
							"<i class=\"glyphicon glyphicon-refresh loop-rotate\"></i> " .
							"{$Translation['Loading ...']}" .
						"</div>\n\t\t\t\t";

					$tabLoaders .= "post('parent-children.php', " . json_encode([
							'ChildTable' => $ChildTable,
							'ChildLookupField' => $ChildLookupField,
							'SelectedID' => $SelectedID,
							'Page' => 1,
							'SortBy' => '',
							'SortDirection' => '',
							'Operation' => 'get-records'
						]) . ", 'panel_{$ChildTable}-{$ChildLookupField}');\n\t\t\t\t";
				}
			}

			if(!$tabLabels) { die('<!-- no children of current parent table are accessible to current user -->'); }
			?>
			<div id="children-tabs">
				<ul class="nav nav-tabs">
					<?php echo $tabLabels; ?>
				</ul>
				<span id="pc-loading"></span>
			</div>
			<div class="tab-content"><?php echo $tabPanels; ?></div>

			<script>
				$j(function() {
					/* for iOS, avoid loading child tabs in modals */
					var iOS = /(iPad|iPhone|iPod)/g.test(navigator.userAgent);
					var embedded = ($j('.navbar').length == 0);
					if(iOS && embedded) {
						$j('#children-tabs').next('.tab-content').remove();
						$j('#children-tabs').remove();
						return;
					}

					/* ajax loading of each tab's contents */
					<?php echo $tabLoaders; ?>

					/* show child field caption on tab title in case the same child table appears more than once */
					$j('.child-field-caption').each(function() {
						var clss = $j(this).attr('class').split(/\s+/).reduce(function(rc, cc) {
							return (cc.match(/child-label-.*/) ? '.' + cc : rc);
						}, '');

						// if class occurs more than once, remove .hidden
						if($j(clss).length > 1) $j(clss).removeClass('hidden');
					})
				})
			</script>
			<?php
			break;

		/************************************************/
		case 'show-children-printable':
			/* populate HTML and JS content with children buttons */
			$tabLabels = $tabPanels = $tabLoaders = '';
			foreach($userPCConfig as $ChildTable => $childLookups) {
				foreach($childLookups as $ChildLookupField => $childConfig) {
					if($childConfig['parent-table'] != $ParentTable) continue;

					$TableIcon = ($childConfig['table-icon'] ? "<img src=\"{$childConfig['table-icon']}\" border=\"0\">" : '');

					$tabLabels .= "<button type=\"button\" class=\"btn btn-default child-tab-print-toggler\" data-target=\"#panel_{$ChildTable}-{$ChildLookupField}\" id=\"tab_{$ChildTable}-{$ChildLookupField}\" data-toggle=\"collapse\">" .
							"{$TableIcon} {$childConfig['tab-label']}" .
							"<span class=\"badge child-count child-count-{$ChildTable}-{$ChildLookupField}\"></span>" .
						"</button>\n\t\t\t\t\t";

					$tabPanels .= "<div id=\"panel_{$ChildTable}-{$ChildLookupField}\" class=\"collapse child-panel-print\">" .
							"<i class=\"glyphicon glyphicon-refresh loop-rotate\"></i> " .
							$Translation['Loading ...'] .
						"</div>\n\t\t\t\t";

					$tabLoaders .= "post('parent-children.php', " . json_encode([
							'ChildTable' => $ChildTable,
							'ChildLookupField' => $ChildLookupField,
							'SelectedID' => $SelectedID,
							'Page' => 1,
							'SortBy' => '',
							'SortDirection' => '',
							'Operation' => 'get-records-printable'
						]) . ", 'panel_{$ChildTable}-{$ChildLookupField}');\n\t\t\t\t";
				}
			}

			if(!$tabLabels) { die('<!-- no children of current parent table are accessible to current user -->'); }
			?>
			<div id="children-tabs" class="hidden-print">
				<div class="btn-group btn-group-lg">
					<?php echo $tabLabels; ?>
				</div>
				<span id="pc-loading"></span>
			</div>
			<div class="vspacer-lg"><?php echo $tabPanels; ?></div>

			<script>
				$j(function() {
					/* for iOS, avoid loading child tabs in modals */
					var iOS = /(iPad|iPhone|iPod)/g.test(navigator.userAgent);
					var embedded = ($j('.navbar').length == 0);
					if(iOS && embedded) {
						$j('#children-tabs').next('.tab-content').remove();
						$j('#children-tabs').remove();
						return;
					}

					/* ajax loading of each tab's contents */
					<?php echo $tabLoaders; ?>
				})
			</script>
			<?php
			break;

		/************************************************/
		case 'get-records-printable':
		default: /* default is 'get-records' */

			if($Operation == 'get-records-printable') {
				$currentConfig['records-per-page'] = 2000;
			}

			// build the user permissions limiter
			$permissionsWhere = $permissionsJoin = '';
			$permChild = getTablePermissions($ChildTable);
			if($permChild['view'] == 1) { // user can view only his own records
				$permissionsWhere = "`$ChildTable`.`{$currentConfig['child-primary-key']}`=`membership_userrecords`.`pkValue` AND `membership_userrecords`.`tableName`='$ChildTable' AND LCASE(`membership_userrecords`.`memberID`)='" . getLoggedMemberID() . "'";
			} elseif($permChild['view'] == 2) { // user can view only his group's records
				$permissionsWhere = "`$ChildTable`.`{$currentConfig['child-primary-key']}`=`membership_userrecords`.`pkValue` AND `membership_userrecords`.`tableName`='$ChildTable' AND `membership_userrecords`.`groupID`='" . getLoggedGroupID() . "'";
			} elseif($permChild['view'] == 3) { // user can view all records
				/* that's the only case remaining ... no need to modify the query in this case */
			}
			$permissionsJoin = ($permissionsWhere ? ", `membership_userrecords`" : '');

			// build the count query
			$forcedWhere = $currentConfig['forced-where'];
			$query = 
				preg_replace('/^select .* from /i', 'SELECT count(1) FROM ', $currentConfig['query']) .
				$permissionsJoin . " WHERE " .
				($permissionsWhere ? "( $permissionsWhere )" : "( 1=1 )") . " AND " .
				($forcedWhere ? "( $forcedWhere )" : "( 2=2 )") . " AND " .
				"`$ChildTable`.`$ChildLookupField`='" . makeSafe($SelectedID) . "'";
			$totalMatches = sqlValue($query);

			// make sure $Page is <= max pages
			$maxPage = ceil($totalMatches / $currentConfig['records-per-page']);
			if($Page > $maxPage) { $Page = $maxPage; }

			// initiate output data array
			$data = [
				'config' => $currentConfig,
				'parameters' => [
					'ChildTable' => $ChildTable,
					'ChildLookupField' => $ChildLookupField,
					'SelectedID' => $SelectedID,
					'Page' => $Page,
					'SortBy' => $SortBy,
					'SortDirection' => $SortDirection,
					'Operation' => $Operation,
				],
				'records' => [],
				'totalMatches' => $totalMatches
			];

			// build the data query
			if($totalMatches) { // if we have at least one record, proceed with fetching data
				$startRecord = $currentConfig['records-per-page'] * ($Page - 1);
				$data['query'] = 
					$currentConfig['query'] .
					$permissionsJoin . " WHERE " .
					($permissionsWhere ? "( $permissionsWhere )" : "( 1=1 )") . " AND " .
					($forcedWhere ? "( $forcedWhere )" : "( 2=2 )") . " AND " .
					"`$ChildTable`.`$ChildLookupField`='" . makeSafe($SelectedID) . "'" . 
					($SortBy !== false && $currentConfig['sortable-fields'][$SortBy] ? " ORDER BY {$currentConfig['sortable-fields'][$SortBy]} $SortDirection" : '') .
					" LIMIT $startRecord, {$currentConfig['records-per-page']}";
				$res = sql($data['query'], $eo);
				while($row = db_fetch_row($res)) {
					$data['records'][$row[$currentConfig['child-primary-key-index']]] = $row;
				}
			} else { // if no matching records
				$startRecord = 0;
			}

			if($Operation == 'get-records-printable') {
				$response = loadView($currentConfig['template-printable'], $data);
			} else {
				$response = loadView($currentConfig['template'], $data);
			}

			// change name space to ensure uniqueness
			$uniqueNameSpace = $ChildTable.ucfirst($ChildLookupField).'GetRecords';
			echo str_replace("{$ChildTable}GetChildrenRecordsList", $uniqueNameSpace, $response);
		/************************************************/
	}
