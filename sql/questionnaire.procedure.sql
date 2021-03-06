delimiter $$

select "Historic Procedure" as step $$

drop procedure if exists hist_create$$
create procedure hist_create(
	in iquestionnaire integer
)
begin
	declare ihist integer;
	insert into historic (questionnaire,creationTime,user) values (iquestionnaire,now(),substring_index(user(),'@',1));
	SELECT LAST_INSERT_ID() into ihist;

	select 0 as error, '$_hist' as message,ihist as hist;
end$$

drop procedure if exists hist_answerInsert$$
create procedure hist_answerInsert(
	in ihist integer,
	in iquestion integer,
	in ianswer varchar (120)
)
begin
	insert into answerRecord (hist,question,answer) values (ihist,iquestion,ianswer);
	select 0 as error;
end$$

select "Questionnaire Procedure" as step $$

drop procedure if exists questionnaire_new$$
create procedure questionnaire_new(
	in idescription varchar(50)
)
begin
	insert into questionnaire (description) values (idescription);
	select 0 as error;
end$$

drop procedure if exists questionnaire_edit$$
create procedure questionnaire_edit(
	in iID integer,
	in idescription varchar(50)
)
begin
	update questionnaire set description=idescription where id = iID ;
	select 0 as error;
end$$


drop procedure if exists questionnaire_delete$$
create procedure questionnaire_delete(
	in iID integer
)
begin
	delete from questionnaire where id = iID ;
	select 0 as error;
end$$

select "questions Procedure" as step $$

drop procedure if exists question_new$$
create procedure question_new(
	in iquestion varchar(60),
	in ilanguage varchar(2),
	in imultivalue boolean
)
begin
	insert into questions (question,language,multivalue) values (iquestion,ilanguage,imultivalue);
	select 0 as error;
end$$	
	
drop procedure if exists question_edit$$
create procedure question_edit(
	in iID integer,
	in iquestion varchar(60),
	in ilanguage varchar(2),
	in imultivalue boolean
)
begin
	update questions set question=iquestion, language=ilanguage,multivalue=imultivalue where id = iID ;
	select 0 as error;
end$$

drop procedure if exists question_delete$$
create procedure question_delete(
	in iID integer
)
begin
	delete from questions where id = iID ;
	select 0 as error;
end$$

select "questionByquestionnaire Procedure" as step $$

drop procedure if exists associateQuestion$$
create procedure associateQuestion(
	in iquestionnaire integer,
	in iquestion integer,
	in ilevel integer
)
begin
	insert into questionByquestionnaire (questionnaire,question,level)values(iquestionnaire,iquestion,ilevel);
	select 0 as error;
end$$

drop procedure if exists disassociateQuestion$$
create procedure disassociateQuestion(
	in iquestionnaire integer,
	in iquestion integer
)
begin
	delete from questionByquestionnaire where question = iquestion and questionnaire =iquestionnaire;
	select 0 as error;
end$$


drop procedure if exists setOrderQuestion$$
create procedure setOrderQuestion(
	in iquestionnaire integer,
	in iquestion integer,
	in ilevel integer
)
begin
	declare sameLevel integer;
	select level into sameLevel from questionByquestionnaire where questionnaire =iquestionnaire and level = ilevel and question <> iquestion ;
	if sameLevel is not null then
		update questionByquestionnaire set level=level+1 where level >= ilevel and questionnaire =iquestionnaire;
	end if;
	update questionByquestionnaire set level=ilevel where question = iquestion and questionnaire =iquestionnaire;

	select 0 as error;
end$$


select "answers Procedure" as step $$

drop procedure if exists answer_new$$
create procedure answer_new(
	in iquestion integer,
	in icaption varchar(30),
	in ilevel integer
)
begin
	declare ianswer integer;
	declare sameLevel integer;
	select max(answer)+1 into ianswer from answerByquestion where question=iquestion;

	if ianswer is null then
		set ianswer = 1;
	end if;
	if ilevel is null then
		set ilevel = ianswer;
	else
		select level into sameLevel from answerByquestion where level = ilevel and question=iquestion;
		if sameLevel is not null then
			update answerByquestion set level=level+1 where level >= ilevel and question=iquestion;
		end if;
	end if;
	insert into answerByquestion (question,answer,caption,level) values (iquestion,ianswer,icaption,ilevel);
	select 0 as error;
end$$

drop procedure if exists answer_edit$$
create procedure answer_edit(
	in iquestion integer,
	in ianswer integer,
	in icaption varchar(30),
	in ilevel integer
)
begin
	declare sameLevel integer;
	
	select level into sameLevel from answerByquestion where level = ilevel and question=iquestion;
	if sameLevel is not null then
		update answerByquestion set level=level+1 where level >= ilevel and question=iquestion;
	end if;

	update answerByquestion set  question=iquestion, answer=ianswer,caption=icaption,level=ilevel where question = iquestion and answer = ianswer;
	select 0 as error;
end$$


drop procedure if exists answer_delete$$
create procedure answer_delete(
	in iquestion integer,
	in ianswer integer
)
begin
	delete from answerByquestion where question = iquestion and answer = ianswer ;
	select 0 as error;
end$$


delimiter ;