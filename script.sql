create schema project_k71;
use project_k71;
create table khoa(
	id int auto_increment,
	makhoa char(10),
    ten nvarchar(50),
    mota text,
    primary key(id)
);
create table user(
	id int auto_increment primary key,
    userName varchar(100),
    password varchar(100),
    firstName nvarchar(50),
    lastName nvarchar(50),
    date_of_birth date
);

create table course(
	id int auto_increment primary key,
    name nvarchar(100),
    image longblob,
    imgpath varchar(100)
);

create table question(
	id int primary key auto_increment,
    user_id int not null,
    course_id int not null,
    ques_type nvarchar(50),
    ques text,
    imgpath varchar(255),
    foreign key (user_id) references user(id),
	foreign key (course_id) references course(id)
);
create table answer(
	id int primary key auto_increment,
    ques_id int,
    ans text,
    isTrue bool,
    foreign key (ques_id) references question(id)
);



insert into user(userName,password) 
values("tk1","e10adc3949ba59abbe56e057f20f883e"),  -- 123456 -- 
("tk2","202cb962ac59075b964b07152d234b70") ,  -- 123 --
("tk3","1bbd886460827015e5d605ed44252251");  -- 11111111 --


insert into course(name,imgpath) 
values("Kiến trúc máy tính","../images/khoahoc.jpg"),
("Hệ quản trị cơ sở dữ liệu","../images/khoahoc.jpg"),
("Ma trận","../images/khoahoc.jpg");

    