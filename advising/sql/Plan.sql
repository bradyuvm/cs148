CREATE TABLE IF NOT EXISTS tblPlan (
  pmkCourseId int(11) NOT NULL AUTO_INCREMENT primary key,
  fldDateCreated TIMESTAMP,




  fldLastName varchar(100) NOT NULL,
  fldFirstName varchar(100) NOT NULL,
  pmkNetId varchar(12) NOT NULL,
  fldSalary int(11) NOT NULL,
  fldPhone varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

