# basic-erp-system
Basic ERP System that includes Purchasing, Receiving, Inventory, and Vendor Management.


SQL

localhost configuration

CREATE DATABASE myapp;
USE myapp;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
);

CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partnumber VARCHAR(50) NOT NULL,
    partdescription VARCHAR(100) NOT NULL,
    partquantity INT NOT NULL
);

CREATE TABLE vendor (
	id INT auto_increment PRIMARY KEY,
    vendorname VARCHAR(50) NOT NULL UNIQUE,
    vendorcity VARCHAR(50) NOT NULL
    );
    
CREATE TABLE purchase (
	id INT auto_increment PRIMARY KEY,
    partnumber VARCHAR(50) NOT NULL,
    partdescription VARCHAR(100) NOT NULL,
    partquantity INT NOT NULL,
    vendorid INT NOT NULL,
    desireddate DATE NOT NULL
    );
