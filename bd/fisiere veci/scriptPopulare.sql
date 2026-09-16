-- Inserare utilizatori
INSERT INTO users (name, surname, email, password, location, is_family, latitude, longitude, rol)
VALUES
('John', 'Doe', 'john.doe@example.com', 'hashed_password_1', 'New York, USA', 1, 40.7128, -74.0060, 'adoptator');

INSERT INTO users (name, surname, email, password, location, is_family, latitude, longitude, rol)
VALUES
('Jane', 'Smith', 'jane.smith@example.com', 'hashed_password_2', 'Los Angeles, USA', 1, 34.0522, -118.2437, 'proprietar');

INSERT INTO users (name, surname, email, password, location, is_family, latitude, longitude, rol)
VALUES
('Mark', 'Taylor', 'mark.taylor@example.com', 'hashed_password_3', 'Chicago, USA', 1, 41.8781, -87.6298, 'adoptator');

INSERT INTO users (name, surname, email, password, location, is_family, latitude, longitude, rol)
VALUES
('Emily', 'Johnson', 'emily.johnson@example.com', 'hashed_password_4', 'Miami, USA', 0, 25.7617, -80.1918, 'proprietar');

INSERT INTO users (name, surname, email, password, location, is_family, latitude, longitude, rol)
VALUES
('Liam', 'Brown', 'liam.brown@example.com', 'hashed_password_5', 'Boston, USA', 1, 42.3601, -71.0589, 'adoptator');

INSERT INTO users (name, surname, email, password, location, is_family, latitude, longitude, rol)
VALUES
('Olivia', 'Davis', 'olivia.davis@example.com', 'hashed_password_6', 'San Francisco, USA', 1, 37.7749, -122.4194, 'proprietar');

INSERT INTO users (name, surname, email, password, location, is_family, latitude, longitude, rol)
VALUES
('Sophia', 'Miller', 'sophia.miller@example.com', 'hashed_password_7', 'Austin, USA', 0, 30.2672, -97.7431, 'adoptator');

-- Inserare animale (asigurați-vă că referințele owner_id sunt valide)
INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude)
VALUES
('Buddy', 'Dog', 'Labrador', 5, 'Male', 'Healthy', 'Friendly and playful dog', 1, 'New York, USA', 2, 40.7128, -74.0060);

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude)
VALUES
('Mittens', 'Cat', 'Persian', 3, 'Female', 'Healthy', 'Loves to cuddle and relax', 1, 'Los Angeles, USA', 3, 34.0522, -118.2437);

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude)
VALUES
('Charlie', 'Dog', 'Bulldog', 2, 'Male', 'Healthy', 'Strong and protective dog', 1, 'Chicago, USA', 4, 41.8781, -87.6298);

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude)
VALUES
('Whiskers', 'Cat', 'Siamese', 4, 'Female', 'Healthy', 'Playful and energetic cat', 1, 'Miami, USA', 1, 25.7617, -80.1918);

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude)
VALUES
('Rex', 'Dog', 'German Shepherd', 3, 'Male', 'Healthy', 'Loyal and intelligent dog', 1, 'Boston, USA', 6, 42.3601, -71.0589);

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude)
VALUES
('Luna', 'Cat', 'Maine Coon', 6, 'Female', 'Healthy', 'Gentle giant who loves attention', 1, 'San Francisco, USA', 7, 37.7749, -122.4194);

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude)
VALUES
('Max', 'Dog', 'Beagle', 4, 'Male', 'Healthy', 'Friendly and energetic', 1, 'Austin, USA', 5, 30.2672, -97.7431);

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude)
VALUES
('Zara', 'Cat', 'Sphynx', 2, 'Female', 'Healthy', 'Very affectionate and loves cuddling', 1, 'Chicago, USA', 4, 41.8781, -87.6298);

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude)
VALUES
('Daisy', 'Dog', 'Golden Retriever', 3, 'Female', 'Healthy', 'Loves kids and is very friendly', 1, 'Miami, USA', 1, 25.7617, -80.1918);

-- Inserare cereri de adopție
INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(1, 3, 'pending');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(2, 1, 'approved');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(3, 2, 'rejection');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(4, 5, 'pending');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(5, 6, 'approved');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(6, 4, 'pending');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(7, 2, 'pending');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(8, 3, 'rejection');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(9, 6, 'approved');

-- Inserare program de hrănire
INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(1, TIMESTAMP '2025-05-10 08:00:00', 'Dry dog food', 'Daily');

INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(2, TIMESTAMP '2025-05-10 09:00:00', 'Wet cat food', 'Daily');

INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(3, TIMESTAMP '2025-05-10 08:30:00', 'Canned dog food', 'Weekly');

INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(4, TIMESTAMP '2025-05-10 09:30:00', 'Premium cat food', 'Occasional');

INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(5, TIMESTAMP '2025-05-10 07:45:00', 'Dry kibble', 'Daily');

INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(6, TIMESTAMP '2025-05-10 10:00:00', 'Canned food', 'Daily');

INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(7, TIMESTAMP '2025-05-10 07:00:00', 'Chicken and rice', 'Weekly');

INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(8, TIMESTAMP '2025-05-10 08:15:00', 'Wet food for cats', 'Daily');

INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(9, TIMESTAMP '2025-05-10 09:00:00', 'Canned food', 'Occasional');

-- Inserare restricții pentru animale
INSERT INTO restrictions (pet_id, description)
VALUES
(1, 'Needs to be kept indoors during rainy weather');

INSERT INTO restrictions (pet_id, description)
VALUES
(2, 'Cannot be around loud noises due to anxiety');

INSERT INTO restrictions (pet_id, description)
VALUES
(3, 'Needs regular exercise to stay healthy');

INSERT INTO restrictions (pet_id, description)
VALUES
(4, 'Cannot be fed chocolate or onions');

INSERT INTO restrictions (pet_id, description)
VALUES
(5, 'Needs regular grooming to avoid matting');

INSERT INTO restrictions (pet_id, description)
VALUES
(6, 'Can only be around calm children due to size');

INSERT INTO restrictions (pet_id, description)
VALUES
(7, 'Should not be left alone for long periods');

INSERT INTO restrictions (pet_id, description)
VALUES
(8, 'Can develop skin rashes if overexerted');

INSERT INTO restrictions (pet_id, description)
VALUES
(9, 'Cannot be in hot climates for too long');

-- Inserare media (foto sau video)
INSERT INTO media (pet_id, type, url)
VALUES
(1, 'photo', 'http://example.com/media/buddy_1.jpg');

INSERT INTO media (pet_id, type, url)
VALUES
(2, 'video', 'http://example.com/media/mittens_video.mp4');

INSERT INTO media (pet_id, type, url)
VALUES
(3, 'photo', 'http://example.com/media/charlie_photo.jpg');

INSERT INTO media (pet_id, type, url)
VALUES
(4, 'photo', 'http://example.com/media/whiskers_photo.jpg');

INSERT INTO media (pet_id, type, url)
VALUES
(5, 'photo', 'http://example.com/media/rex_photo.jpg');

INSERT INTO media (pet_id, type, url)
VALUES
(6, 'video', 'http://example.com/media/luna_video.mp4');

INSERT INTO media (pet_id, type, url)
VALUES
(7, 'photo', 'http://example.com/media/max_photo.jpg');

INSERT INTO media (pet_id, type, url)
VALUES
(8, 'photo', 'http://example.com/media/zara_photo.jpg');

INSERT INTO media (pet_id, type, url)
VALUES
(9, 'video', 'http://example.com/media/daisy_video.mp4');

-- Inserare istoricul medical al animalelor
INSERT INTO medical_history (pet_id, description, first_aid_method)
VALUES
(1, 'Routine vaccination and check-up', 'None');

INSERT INTO medical_history (pet_id, description, first_aid_method)
VALUES
(2, 'Dental cleaning and health check-up', 'None');

INSERT INTO medical_history (pet_id, description, first_aid_method)
VALUES
(3, 'Treating minor leg injury', 'Applied bandage and rest');

INSERT INTO medical_history (pet_id, description, first_aid_method)
VALUES
(4, 'Routine vaccination and check-up', 'None');

INSERT INTO medical_history (pet_id, description, first_aid_method)
VALUES
(5, 'Routine health check-up', 'None');

INSERT INTO medical_history (pet_id, description, first_aid_method)
VALUES
(6, 'Treatment for flea infestation', 'Administered flea treatment');

INSERT INTO medical_history (pet_id, description, first_aid_method)
VALUES
(7, 'Routine health check-up', 'None');

INSERT INTO medical_history (pet_id, description, first_aid_method)
VALUES
(8, 'Routine vaccination and check-up', 'None');

INSERT INTO medical_history (pet_id, description, first_aid_method)
VALUES
(9, 'Treatment for minor skin rash', 'Applied ointment and rest');

-- Inserare feed RSS
INSERT INTO rss_feed (title, content, pet_id, location, popularity_score, is_general_news)
VALUES
('Buddy is up for adoption!', 'Buddy is a friendly and playful dog looking for a new home.', 1, 'New York, USA', 9.5, 0);

INSERT INTO rss_feed (title, content, pet_id, location, popularity_score, is_general_news)
VALUES
('Mittens needs a family!', 'Mittens is a loving Persian cat looking for her forever family.', 2, 'Los Angeles, USA', 8.3, 0);

INSERT INTO rss_feed (title, content, pet_id, location, popularity_score, is_general_news)
VALUES
('Charlie the Bulldog', 'Charlie is a strong and protective dog. He is ready for a new home.', 3, 'Chicago, USA', 8.7, 0);

INSERT INTO rss_feed (title, content, pet_id, location, popularity_score, is_general_news)
VALUES
('Whiskers is waiting for you!', 'Whiskers is a playful and energetic Siamese cat who loves attention.', 4, 'Miami, USA', 9.0, 0);

INSERT INTO rss_feed (title, content, pet_id, location, popularity_score, is_general_news)
VALUES
('Rex the German Shepherd', 'Rex is a loyal and intelligent dog. Looking for a forever family.', 5, 'Boston, USA', 8.5, 0);

INSERT INTO rss_feed (title, content, pet_id, location, popularity_score, is_general_news)
VALUES
('Luna the Maine Coon', 'Luna is a gentle giant who loves attention. Adopt her today!', 6, 'San Francisco, USA', 9.2, 0);

INSERT INTO rss_feed (title, content, pet_id, location, popularity_score, is_general_news)
VALUES
('Max the Beagle', 'Max is friendly and energetic. Adopt him now!', 7, 'Austin, USA', 8.0, 0);

INSERT INTO rss_feed (title, content, pet_id, location, popularity_score, is_general_news)
VALUES
('Zara the Sphynx', 'Zara is affectionate and loves cuddling. Adopt her now!', 8, 'Chicago, USA', 9.3, 0);

INSERT INTO rss_feed (title, content, pet_id, location, popularity_score, is_general_news)
VALUES
('Daisy the Golden Retriever', 'Daisy is very friendly and loves kids. Adopt her today!', 9, 'Miami, USA', 9.5, 0);