--
-- 	Database Table Creation
--
--	This file will create the tables for FindAHomeVancouver project
--  by Raifah Rahman and Sarah Li

-- Credits: Used bookbiz.sql file from CPSC 304 Tutorial 5 for file outline
--          Used all.sql file from CPSC 304 Canvas course page for file outline


--  First drop any existing tables. Any errors are ignored.
-- Order in which tables are dropped matters!
drop table GreaterVancouverRegion cascade constraints;
drop table Seller cascade constraints;
drop table RealEstateAgent cascade constraints;
drop table PropertyListing cascade constraints;
drop table House cascade constraints;
drop table Apartment cascade constraints;
drop table Land cascade constraints;
drop table Townhouse cascade constraints;
drop table PropertyType cascade constraints;
drop table Client cascade constraints;
-- drop table PropertyListingHasPropertyType cascade constraints;
drop table Purchase cascade constraints;
drop table PurchaseWithRealEstateAgent cascade constraints;

create table GreaterVancouverRegion
	(CityName varchar(30),
	ListingCount int,
	primary key (CityName));

grant select on GreaterVancouverRegion to public;


create table Seller
	(SellerID int,
	Name varchar(30),
	primary key (SellerID));

grant select on Seller to public;


create table RealEstateAgent
	(AgentID int not null,
	REAName varchar(20),
	Phone int,
	Email varchar(30),
	Rate int,
	Brokerage varchar(80),
	primary key (AgentID));
 
grant select on RealEstateAgent to public;


create table PropertyListing 
	(ListingID int not null,
	CityName varchar(30) not null,
	SellerID int not null,
	AgentID int not null,
	-- PropertyTypeID char(1) not null,
	PropertyAddress varchar(50),
	ListingPrice int,
	PropertyStatus varchar(10),
	SquareFootage int,
	ListingDate date,
	primary key (ListingID),
	foreign key (CityName) references GreaterVancouverRegion ON DELETE CASCADE,
	foreign key (SellerID) references Seller ON DELETE CASCADE,
	foreign key (AgentID) references RealEstateAgent ON DELETE CASCADE);
	--foreign key (PropertyTypeID) references PropertyType ON DELETE CASCADE);

grant select on PropertyListing to public; 


create table PropertyType
	(PropertyTypeID int not null,
	ListingID int not null,
	BedroomCount int,
	BathroomCount int,
	primary key (PropertyTypeID),
	foreign key (ListingID) references PropertyListing ON DELETE CASCADE);

grant select on PropertyType to public;


create table House
	(PropertyTypeID int not null,
	RentalSuite char(1),
	primary key (PropertyTypeID),
	foreign key (PropertyTypeID) references PropertyType ON DELETE CASCADE);
 
grant select on House to public;
 

create table Apartment
	(PropertyTypeID int not null,
	HasPool char(1),
	HasGym char(1),
	primary key (PropertyTypeID),
	foreign key (PropertyTypeID) references PropertyType ON DELETE CASCADE);
 
grant select on Apartment to public;


create table Land
	(PropertyTypeID int not null,
	PotentialBuildingsCount int,
	primary key (PropertyTypeID),
	foreign key (PropertyTypeID) references PropertyType ON DELETE CASCADE);
 
grant select on Land to public;


create table Townhouse
	(PropertyTypeID int not null,
	HOAFee int,
	primary key (PropertyTypeID),
	foreign key (PropertyTypeID) references PropertyType ON DELETE CASCADE);
 
grant select on Townhouse to public;


create table Client
	(ClientID int not null,
	ClientName varchar(20),
	Phone int,
	Email varchar(30),
	primary key (ClientID));
 
grant select on Client to public;


-- create table PropertyListingHasPropertyType
-- 	(ListingID int not null,
-- 	PropertyTypeID int not null,
-- 	primary key (ListingID, PropertyTypeID),
-- 	foreign key (PropertyTypeID) references PropertyType ON DELETE CASCADE,
-- 	foreign key (ListingID) references PropertyListing ON DELETE CASCADE);
	
-- grant select on PropertyListingHasPropertyType to public;


create table Purchase
	(PurchaseID int not null,
	ClientID int not null,
	SellingPrice int,
	PurchaseDate date, 
	primary key (PurchaseID),
	foreign key (ClientID) references Client ON DELETE CASCADE);
 
grant select on Purchase to public;

create table PurchaseWithRealEstateAgent
	(AgentID int not null,
	PurchaseID int not null,
	primary key (AgentID, PurchaseID),
	foreign key (AgentID) references RealEstateAgent ON DELETE CASCADE,
	foreign key (PurchaseID) references Purchase ON DELETE CASCADE);
 
grant select on PurchaseWithRealEstateAgent to public;


-- done adding all of the tables, now add in some tuples
-- Order of inserts matters!

-- add tuples for GreaterVancouverRegion
insert into GreaterVancouverRegion
values('Surrey', 430);

insert into GreaterVancouverRegion
values('Burnaby', 276);

insert into GreaterVancouverRegion
values('Richmond', 198);

insert into GreaterVancouverRegion
values('Vancouver', 382);

insert into GreaterVancouverRegion
values('Coquitlam', 221);


-- add tuples for Seller
insert into Seller
values(10, 'Alex');

insert into Seller
values(11, 'Bob');

insert into Seller
values(12, 'Selena');

insert into Seller
values(13, 'Mary');

insert into Seller
values(14, 'Jane');


-- add tuples for RealEstateAgent
insert into RealEstateAgent
values(11, 'Ria', 7785409888, 'riaeaton@gmail.com', 20, 'Westcoast Rentals');

insert into RealEstateAgent
values(64, 'Sam', 6042122232, 'sambates@gmail.com', 36, 'ABC Housing');

insert into RealEstateAgent
values(615, 'Leo', 7789906770, 'leo245@gmail.com', 43, 'Downtown Suites');

insert into RealEstateAgent
values(190, 'Nick', 6045119541, 'nick13@gmail.com', 30, 'Garys Housing');

insert into RealEstateAgent
values(19, 'Dina', 2267093340, 'dinawakes15@gmail.com', 28, 'Lionel Estates');

insert into RealEstateAgent
values(10, 'Russell', 7785679008, 'russell12@gmail.com', 25, 'Westcoast Rentals');

insert into RealEstateAgent
values(2, 'Sarah', 6043309821, 'notsarahli@gmail.com', 32, 'ABC Housing');

insert into RealEstateAgent
values(56, 'Liam', 2267860121, 'liamhawking@gmail.com', 40, 'Downtown Suites');

insert into RealEstateAgent
values(34, 'Nicholas', 6042314436, 'nicholasdames@gmail.com', 36, 'Garys Housing');

insert into RealEstateAgent
values(249, 'Amanda', 2260035621, 'amandabailey15@gmail.com', 27, 'Lionel Estates');


-- add tuples for PropertyListing
insert into PropertyListing
values(1, 'Surrey', 10 , 11,
'1234 Johnson', 1234567, 'on market', 3000, TO_DATE('2020-12-09','YYYY-MM-DD'));
 
insert into PropertyListing
values(2, 'Burnaby', 11 , 615,
'1234 Applefarm', 200000, 'on market', 10000, TO_DATE('2022-12-09','YYYY-MM-DD'));
 
insert into PropertyListing
values(3, 'Richmond', 12 , 19,
'2912 Fitzgerald', 5300000, 'sold', 1000, TO_DATE('2022-01-02','YYYY-MM-DD'));
 
insert into PropertyListing
values(4, 'Vancouver', 13 , 64,
'6745 Cornell', 2400000, 'on market', 10000, TO_DATE('2022-10-03','YYYY-MM-DD'));
 
insert into PropertyListing
values(5, 'Coquitlam', 14 , 190,
'7845 Newton', 6300000, 'sold', 1000, TO_DATE('2019-08-17','YYYY-MM-DD'));

insert into PropertyListing
values(6, 'Surrey', 10 , 190,
'1098 Stewart', 1500000, 'on market', 3500, TO_DATE('2021-11-09','YYYY-MM-DD'));
 
insert into PropertyListing
values(7, 'Burnaby', 11 , 64,
'8541 Crescent', 2300000, 'sold', 10000, TO_DATE('2022-12-09','YYYY-MM-DD'));
 
insert into PropertyListing
values(8, 'Richmond', 12 , 11,
'2765 Shining', 347000, 'sold', 1000, TO_DATE('2022-01-02','YYYY-MM-DD'));
 
insert into PropertyListing
values(9, 'Vancouver', 13 , 615,
'5329 Dunbar', 3000, 'on market', 10000, TO_DATE('2022-10-03','YYYY-MM-DD'));
 
insert into PropertyListing
values(10, 'Coquitlam', 14 , 19,
'3325 Lion', 2500, 'sold', 1000, TO_DATE('2019-08-17','YYYY-MM-DD'));

insert into PropertyListing
values(11, 'Surrey', 10 , 10,
'1128 Aberdeen', 123200, 'sold', 3000, TO_DATE('2018-10-23','YYYY-MM-DD'));
 
insert into PropertyListing
values(12, 'Burnaby', 11 , 2,
'8723 Victoria', 2500000, 'sold', 10000, TO_DATE('2022-10-14','YYYY-MM-DD'));
 
insert into PropertyListing
values(13, 'Richmond', 12 , 56,
'4521 Smith', 53000000, 'sold', 1000, TO_DATE('2021-07-02','YYYY-MM-DD'));
 
insert into PropertyListing
values(14, 'Vancouver', 13 , 34,
'9500 Abbott', 2700000, 'on market', 10000, TO_DATE('2020-08-08','YYYY-MM-DD'));
 
insert into PropertyListing
values(15, 'Coquitlam', 14 , 249,
'3219 Essex', 63000000, 'on market', 1000, TO_DATE('2019-04-11','YYYY-MM-DD'));

insert into PropertyListing
values(16, 'Surrey', 10 , 34,
'6925 Hope', 800000, 'on market', 3000, TO_DATE('2018-01-09','YYYY-MM-DD'));
 
insert into PropertyListing
values(17, 'Burnaby', 11 , 10,
'2012 Boulevard', 2000000, 'sold', 10000, TO_DATE('2021-12-25','YYYY-MM-DD'));
 
insert into PropertyListing
values(18, 'Richmond', 12 , 2,
'7652 Deer', 53000000, 'sold', 1000, TO_DATE('2019-02-14','YYYY-MM-DD'));
 
insert into PropertyListing
values(19, 'Vancouver', 13 , 56,
'5070 Hastings', 2400000, 'on market', 10000, TO_DATE('2020-07-15','YYYY-MM-DD'));
 
insert into PropertyListing
values(20, 'Coquitlam', 14 , 249,
'3211 Cardell', 63000000, 'sold', 1000, TO_DATE('2022-11-17','YYYY-MM-DD'));

insert into PropertyListing
values(21, 'Surrey', 10 , 11,
'6301 Bains', 1700, 'on market', 3000, TO_DATE('2018-01-09','YYYY-MM-DD'));
 
insert into PropertyListing
values(22, 'Burnaby', 11 , 190,
'2022 Hell', 1100, 'sold', 10000, TO_DATE('2021-12-25','YYYY-MM-DD'));
 
insert into PropertyListing
values(23, 'Richmond', 12 , 19,
'7096 Prima', 5000000, 'sold', 1000, TO_DATE('2019-02-14','YYYY-MM-DD'));
 
insert into PropertyListing
values(24, 'Vancouver', 13 , 2,
'5112 Hornby', 2400000, 'on market', 10000, TO_DATE('2020-07-15','YYYY-MM-DD'));
 
insert into PropertyListing
values(25, 'Coquitlam', 14 , 10,
'0078 Plaza', 645000, 'sold', 1000, TO_DATE('2022-11-17','YYYY-MM-DD'));
 
 

-- add tuples for PropertyType
insert into PropertyType
values (1, 1, 2, 3);
 
insert into PropertyType
values (2, 2, 1, 2);
 
insert into PropertyType
values (3, 3, 4, 4);
 
insert into PropertyType
values (4, 4,  3, 2);
 
insert into PropertyType
values (5, 5, 2, 1);

insert into PropertyType
values (6, 6,  2, 3);
 
insert into PropertyType
values (7, 7, 1, 2);
 
insert into PropertyType
values (8, 8,  4, 4);
 
insert into PropertyType
values (9, 9,  3, 2);
 
insert into PropertyType
values (10, 10,  2, 1);

insert into PropertyType
values (11, 11,  2, 3);
 
insert into PropertyType
values (12, 12,  1, 2);
 
insert into PropertyType
values (13, 13,  4, 4);
 
insert into PropertyType
values (14, 14,  3, 2);
 
insert into PropertyType
values (15, 15,  2, 1);

insert into PropertyType
values (16, 16,  2, 3);
 
insert into PropertyType
values (17, 17,  1, 2);
 
insert into PropertyType
values (18, 18,  4, 4);
 
insert into PropertyType
values (19, 19,  3, 2);
 
insert into PropertyType
values (20, 20, 4, 1);

insert into PropertyType
values (21, 21,  2, 1);
 
insert into PropertyType
values (22, 22,  1, 1);
 
insert into PropertyType
values (23, 23,  2, 4);
 
insert into PropertyType
values (24, 24,  3, 1);
 
insert into PropertyType
values (25, 25, 6, 1);


-- -- add tuples for PropertyListingHasPropertyType
-- insert into PropertyListingHasPropertyType
-- values(1, 1);
 
-- insert into PropertyListingHasPropertyType
-- values(2, 2);
 
-- insert into PropertyListingHasPropertyType
-- values(3, 3);

-- insert into PropertyListingHasPropertyType
-- values(4, 4);
 
-- insert into PropertyListingHasPropertyType
-- values(5, 5);

-- insert into PropertyListingHasPropertyType
-- values(6, 6);
 
-- insert into PropertyListingHasPropertyType
-- values(7, 7);
 
-- insert into PropertyListingHasPropertyType
-- values(8, 8);

-- insert into PropertyListingHasPropertyType
-- values(9, 9);
 
-- insert into PropertyListingHasPropertyType
-- values(10, 10);

-- insert into PropertyListingHasPropertyType
-- values(11, 11);
 
-- insert into PropertyListingHasPropertyType
-- values(12, 12);
 
-- insert into PropertyListingHasPropertyType
-- values(13, 13);

-- insert into PropertyListingHasPropertyType
-- values(14, 14);
 
-- insert into PropertyListingHasPropertyType
-- values(15, 15);

-- insert into PropertyListingHasPropertyType
-- values(16, 16);
 
-- insert into PropertyListingHasPropertyType
-- values(17, 17);
 
-- insert into PropertyListingHasPropertyType
-- values(18, 18);

-- insert into PropertyListingHasPropertyType
-- values(19, 19);
 
-- insert into PropertyListingHasPropertyType
-- values(20, 20);
 
 
 -- add tuples for House 
insert into House
values(1, 'y');
 
insert into House
values(2 , 'n');
 
insert into House
values(3, 'y');
 
insert into House
values(4, 'n');
 
insert into House
values(5, 'n');

 
 -- add tuples for Apartment 
insert into Apartment
values (6, 'y', 'n');
 
insert into Apartment
values (7, 'n', 'y');

insert into Apartment
values (8, 'n', 'y');
 
insert into Apartment
values (9, 'y', 'n');
 
insert into Apartment
values (10, 'y', 'y');

insert into Apartment
values (21, 'y', 'n');
 
insert into Apartment
values (22, 'n', 'n');

insert into Apartment
values (23, 'n', 'y');
 
insert into Apartment
values (24, 'y', 'n');
 
insert into Apartment
values (25, 'y', 'y');
 
 
 --add tuples for Land 
insert into Land 
values (11, 2);
 
insert into Land 
values (12, 10);
 
insert into Land 
values (13, 5);
 
insert into Land 
values (14, 1);
 
insert into Land 
values (15, 3);
 
 
 -- add tuples for Townhouse 
insert into Townhouse 
values(16,706);
 
insert into Townhouse
values(17,167);
 
insert into Townhouse
values(18,131);
 
insert into Townhouse
values(19,713);
 
insert into Townhouse
values(20,804);
 
 
 -- add tuples for Client
insert into Client
values(10, 'Claire', 6045467654, 'claire13@gmail.com');
 
insert into Client
values(24, 'John', 7783452134, 'johndoe@yahoo.com');
 
insert into Client
values(31, 'Melissa', 7780945614, 'melissabridges13@gmail.com');
 
insert into Client
values(4, 'David', 2265412110, 'david1222@yahoo.com');
 
insert into Client
values(52, 'Diana', 2260987760, 'dianafields14@gmail.com');
 

-- add tuples for Purchase
insert into Purchase
values(12, 10, 34000, TO_DATE('2020-10-09','YYYY-MM-DD'));

insert into Purchase
values(10, 24, 1200, TO_DATE('2021-09-16','YYYY-MM-DD'));

insert into Purchase
values(91, 31, 1200000, TO_DATE('2019-03-10','YYYY-MM-DD'));

insert into Purchase
values(123, 4, 50000, TO_DATE('2020-04-20','YYYY-MM-DD'));

insert into Purchase
values(200, 52, 3000, TO_DATE('2021-01-17','YYYY-MM-DD'));


-- add tuples for PurchaseWithRealEstateAgent
-- (AgentID, PurchaseID)
insert into PurchaseWithRealEstateAgent
values(11, 12);

insert into PurchaseWithRealEstateAgent
values(64, 12);

insert into PurchaseWithRealEstateAgent
values(11, 10);

insert into PurchaseWithRealEstateAgent
values(190, 10);

insert into PurchaseWithRealEstateAgent
values(19, 91);

insert into PurchaseWithRealEstateAgent
values(11, 91);

insert into PurchaseWithRealEstateAgent
values(2, 91);

insert into PurchaseWithRealEstateAgent
values(56, 123);

insert into PurchaseWithRealEstateAgent
values(34, 123);

insert into PurchaseWithRealEstateAgent
values(11, 123);

insert into PurchaseWithRealEstateAgent
values(56, 200);

insert into PurchaseWithRealEstateAgent
values(34, 200);

insert into PurchaseWithRealEstateAgent
values(2, 10);

insert into PurchaseWithRealEstateAgent
values(10, 12);

insert into PurchaseWithRealEstateAgent
values(19, 10);

insert into PurchaseWithRealEstateAgent
values(19, 12);

insert into PurchaseWithRealEstateAgent
values(19, 200);

insert into PurchaseWithRealEstateAgent
values(64, 200);

insert into PurchaseWithRealEstateAgent
values(19, 123);

insert into PurchaseWithRealEstateAgent
values(2, 12);

insert into PurchaseWithRealEstateAgent
values(2, 123);

insert into PurchaseWithRealEstateAgent
values(2, 200);

-- get inserted tuples to display
COMMIT;