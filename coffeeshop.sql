create database if not exists coffeeshop;
use coffeeshop;

create table if not exists users (
    id int primary key auto_increment,
    username varchar(255) not null,
    password varchar(255) not null,
    role enum ('Manager', 'Barista', 'Customer') not null,
    login_status bit(1) default null
);

create table if not exists menu_items (
    id int primary key auto_increment,
    name varchar(255) not null,
    price decimal(10, 2) not null,
    availability boolean not null default true
);

create table if not exists orders (
    id int primary key auto_increment,
    user_id int not null,
    status enum('Pending', 'Confirmed', 'Completed') not null,
    total decimal(10, 2) not null,
    foreign key (user_id) references users(id)
);

create table if not exists order_items (
    id int primary key auto_increment,
    order_id int not null,
    menu_item_id int not null,
    quantity int not null,
    foreign key (order_id) references orders(id),
    foreign key (menu_item_id) references menu_items(id)
);