create table userinfo (
	username CHAR(150) NOT NULL,
	password CHAR(50) NOT NULL,
	token CHAR(15) NOT NULL,
	primary key (username)
) engine = INNODB DEFAULT character SET = utf8 COLLATE = utf8_general_ci;


create table stories(
	storyName CHAR(255) NOT NULL,
	storyContent TEXT NOT NULL,
	primary key (storyName)
) engine = INNODB DEFAULT character SET = utf8 COLLATE = utf8_general_ci;


create table comments(
	username CHAR(150) NOT NULL,
	comment TEXT NOT NULL,
	storyName CHAR(255) NOT NULL,
	posted TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	primary key (username, storyName, posted),
	index(storyName),
	foreign key (storyName) references stories (storyName) on update cascade on delete cascade
) engine = INNODB DEFAULT character SET = utf8 COLLATE = utf8_general_ci;


create table links(
	username CHAR(150) NOT NULL,
	storyName CHAR(255) NOT NULL,
	primary key (username, storyName),
	index(storyName),
	foreign key (storyName) references stories (storyName) on update cascade on delete cascade
) engine = INNODB DEFAULT character SET = utf8 COLLATE = utf8_general_ci;


create table friends(
	username CHAR(150) NOT NULL,
	friend CHAR(150) NOT NULL,
	primary key (username, friend),
	index(username),
	foreign key (username) references userinfo (username) on update cascade on delete cascade
) engine = INNODB DEFAULT character SET = utf8 COLLATE = utf8_general_ci;












create table test(
	username CHAR(150) NOT NULL,
	comment TEXT NOT NULL,
	storyName CHAR(255) NOT NULL,
	primary key (username, storyName),
	index(storyName),
	foreign key (storyName) references stories (storyName) on update cascade on delete cascade
) engine = INNODB DEFAULT character SET = utf8 COLLATE = utf8_general_ci;


create table test(
	username CHAR(150) NOT NULL,
	comment TEXT NOT NULL,
	storyName CHAR(255) NOT NULL,
	primary key (username, storyName),
	index(storyName),
	foreign key (storyName) references stories (storyName) on update cascade on delete cascade,
	index(username),
	foreign key (username) references userinfo(username) on update cascade on delete cascade
) engine = INNODB DEFAULT character SET = utf8 COLLATE = utf8_general_ci;
