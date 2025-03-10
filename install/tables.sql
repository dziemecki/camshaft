CREATE TABLE IF NOT EXISTS users( 
uid INT NOT NULL AUTO_INCREMENT, 
uname VARCHAR(20) NOT NULL, 
upass VARCHAR(60) NOT NULL, 
umail VARCHAR(75) NOT NULL, 
uphone VARCHAR(25), 
active TINYINT, 
lname VARCHAR(50), 
fname VARCHAR(30), 
mname VARCHAR(30), 
pname VARCHAR(30), 
create_date INT NOT NULL, 
change_date INT, 
change_uid INT, 
intid VARCHAR(30), 
reset_key VARCHAR(6), 
PRIMARY KEY ( uid ))

INSERT INTO users (uname, upass, uphone, umail, active, pname, create_date, change_date, change_uid, intid) 
VALUES ('admin', '$2y$10$2ZHhkEJtQF1wK9slR5xDsune4ZNeWTx0u.s41F/B/5ZfGPLDAPnnC', '', 'changeaddress@example.com', '1', 'Administrator', '1731006937', '1731006937', '0', '0')

CREATE TABLE IF NOT EXISTS roles( 
rid INT NOT NULL AUTO_INCREMENT, 
rname VARCHAR(20) NOT NULL, 
rdesc VARCHAR(75) NOT NULL, 
PRIMARY KEY ( rid ))

INSERT INTO roles (rname, rdesc) VALUES ('administrator', 'Full site administration')
INSERT INTO roles (rname, rdesc) VALUES ('superuser', 'Expanded privileges')
INSERT INTO roles (rname, rdesc) VALUES ('user', 'Minimal privileges')
INSERT INTO roles (rname, rdesc) VALUES ('terminal', 'Exempt machine interface')

CREATE TABLE IF NOT EXISTS userroles( 
id INT NOT NULL AUTO_INCREMENT, 
uid INT NOT NULL,
rid INT NOT NULL,
create_date INT NOT NULL,
change_uid INT,	   
PRIMARY KEY ( id ),
FOREIGN KEY (uid) REFERENCES users(uid),
FOREIGN KEY (rid) REFERENCES roles(rid))

INSERT INTO userroles (uid, rid, create_date, change_uid) VALUES ('1', '1', '1731006937', '0')
INSERT INTO userroles (uid, rid, create_date, change_uid) VALUES ('1', '4', '1731006937', '0')

CREATE TABLE IF NOT EXISTS menu( 
mid INT NOT NULL AUTO_INCREMENT, 
mname VARCHAR(20) NOT NULL, 
dname VARCHAR(20) NOT NULL, 
maddr VARCHAR(70) NOT NULL, 		   
weight TINYINT NOT NULL, 
mroles VARCHAR(70), 
active TINYINT NOT NULL, 
PRIMARY KEY ( mid ))

INSERT INTO menu (mname, dname, maddr, weight, mroles, active) VALUES ('site_config', 'Site Configuration', 'site_config', '-49', '1', '1')
INSERT INTO menu (mname, dname, maddr, weight, mroles, active) VALUES ('home', 'Home', 'home', '-50', '1,2,3', '1')
INSERT INTO menu (mname, dname, maddr, weight, mroles, active) VALUES ('logout', 'Log Out', 'access-logout', '50', '1,2,3', '1')

CREATE TABLE IF NOT EXISTS features( 
fid VARCHAR(20) NOT NULL, 
active TINYINT NULL, 
vars JSON NULL, 
PRIMARY KEY ( fid ))   

CREATE TABLE IF NOT EXISTS wiki(
wid int(11) NOT NULL AUTO_INCREMENT, 
module varchar(20) NOT NULL, 
ordinal int(11) NOT NULL, 
title tinytext,
content text, 
PRIMARY KEY (wid), 
UNIQUE KEY wid_UNIQUE (wid))
