-- Create database demo
CREATE DATABASE demo;
GO

-- Use the demo database
USE demo;
GO

-- Create Patients database table
CREATE TABLE Patients (
    patient_id INT PRIMARY KEY IDENTITY,
    email NVARCHAR(80),
    first_name NVARCHAR(80),
    last_name NVARCHAR(80),
    date_of_birth DATE,
    ratingScore INT,
    SubmissionDate DATETIME DEFAULT GETDATE()
    -- age AS DATEDIFF(YEAR, date_of_birth, GETDATE())
);
GO

-- Create PatientPainDetails database table
CREATE TABLE PatientPainDetails (
    id INT PRIMARY KEY IDENTITY,
    patient_id INT FOREIGN KEY REFERENCES Patients(patient_id),
    pain_clinic_rating INT,
    pain_worst_rating INT,
    pain_least_rating INT,
    pain_average_rating INT,
    pain_right_now_rating INT,
    effect_on_activity INT,
    effect_on_mood INT,
    effect_on_walking INT,
    effect_on_work INT,
    effect_on_people INT,
    effect_on_sleep INT,
    effect_on_enjoyment INT,
);
GO

