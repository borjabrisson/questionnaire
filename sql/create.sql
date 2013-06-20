drop database questionnaire;
create database questionnaire;
use questionnaire

select 'Creating tables' as step;
source questionnaire.sql

select 'Loading data' as step;
source questionnaire.data.sql.sql;

-- select 'Configuring imywa' as step;
-- source questionnaire.imywa.sql;

-- -- select questions.question from questions left join questionByquestionnaire on questionByquestionnaire.question = questions.id where questionByquestionnaire.questionnaire = 1 order by level asc;

-- select answer,caption,answerByquestion.level from questionByquestionnaire left join answerByquestion on answerByquestion.question = questionByquestionnaire.question where questionByquestionnaire.level = 11 order by answerByquestion.level asc;
