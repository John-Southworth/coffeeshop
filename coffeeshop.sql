create database if not exists coffeeshop;
use coffeeshop;

create table if not exists users (
    id int primary key auto_increment,
    username varchar(255) not null,
    password varchar(255) not null,
    role enum ('Manager', 'Barista', 'Customer') not null,
    login_status bit(1) default null
);