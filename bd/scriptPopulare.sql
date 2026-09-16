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
INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude, 
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime, 
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Buddy', 'Dog', 'Labrador', 5, 'Male', 'Healthy', 'Friendly and playful dog', 1, 'New York, USA', 2, 40.7128, -74.0060,
    'Friendly, outgoing, and great with people of all ages. Loves to play fetch and give kisses.',
    'Needs daily walks and playtime. Enjoys running in the park and playing with toys.',
    'Eats premium dry dog food twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with fenced yard',
    'Gets along well with other dogs',
    'Golden',
    'Mare',
    1,
    '3 years',
    'Moving to a place that doesn''t allow pets',
    1,
    'Current family has had Buddy since he was a puppy. Very caring and responsible owners.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Mittens', 'Cat', 'Persian', 3, 'Female', 'Healthy', 'Loves to cuddle and relax', 1, 'Los Angeles, USA', 3, 34.0522, -118.2437,
    'Sweet and gentle personality. Loves to be brushed and pampered.',
    'Indoor cat who enjoys window watching and playing with feather toys.',
    'Premium cat food with occasional wet food treats.',
    'Calm household preferred',
    'Indoor only',
    'Good with other calm cats',
    'White with grey patches',
    'Medie',
    1,
    '2 years',
    'Owner relocating internationally',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Charlie', 'Dog', 'Bulldog', 2, 'Male', 'Healthy', 'Strong and protective dog', 1, 'Chicago, USA', 4, 41.8781, -87.6298,
    'Loyal and protective. Great guard dog with a gentle heart.',
    'Moderate exercise needed. Enjoys short walks and playing in the yard.',
    'Special diet for bulldogs, measured portions twice daily.',
    'Moderate activity level',
    'House or apartment with air conditioning',
    'Prefers to be the only pet',
    'Brindle',
    'Medie',
    1,
    '1.5 years',
    'Family medical issues',
    1,
    'Dedicated owner who has invested in training and socialization.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Whiskers', 'Cat', 'Siamese', 4, 'Female', 'Healthy', 'Playful and energetic cat', 1, 'Miami, USA', 1, 25.7617, -80.1918,
    'Curious and active, loves to explore and play with toys.',
    'Needs daily playtime and interaction. Enjoys chasing laser pointers and playing with feather toys.',
    'Eats premium dry cat food twice a day. Enjoys occasional wet food treats.',
    'Active household preferred',
    'House with access to outdoors',
    'Gets along well with other cats',
    'Tortoiseshell',
    'Medie',
    1,
    '2 years',
    'Moving to a place without access to outdoors',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Rex', 'Dog', 'German Shepherd', 3, 'Male', 'Healthy', 'Loyal and intelligent dog', 1, 'Boston, USA', 6, 42.3601, -71.0589,
    'Intelligent and loyal, great with children and other pets.',
    'Needs daily walks and playtime. Enjoys running in the park and playing with toys.',
    'Eats premium dry dog food twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with fenced yard',
    'Prefers to be the only pet',
    'Black and tan',
    'Mare',
    1,
    '4 years',
    'Family moving to a place without access to outdoors',
    1,
    'Dedicated owner who has invested in training and socialization.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Luna', 'Cat', 'Maine Coon', 6, 'Female', 'Healthy', 'Gentle giant who loves attention', 1, 'San Francisco, USA', 7, 37.7749, -122.4194,
    'Sweet and gentle, loves to be petted and cuddled.',
    'Needs daily playtime and interaction. Enjoys lounging on laps and playing with feather toys.',
    'Eats premium dry cat food twice a day. Enjoys occasional wet food treats.',
    'Calm household preferred',
    'House with access to outdoors',
    'Gets along well with other cats',
    'Brown tabby',
    'Mare',
    1,
    '5 years',
    'Owner relocating internationally',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Max', 'Dog', 'Beagle', 4, 'Male', 'Healthy', 'Friendly and energetic', 1, 'Austin, USA', 5, 30.2672, -97.7431,
    'Curious and friendly, loves to explore and play with toys.',
    'Needs daily walks and playtime. Enjoys running in the park and playing with toys.',
    'Eats premium dry dog food twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with access to outdoors',
    'Gets along well with other dogs',
    'Tri-color',
    'Mare',
    1,
    '2 years',
    'Family moving to a place without access to outdoors',
    1,
    'Dedicated owner who has invested in training and socialization.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Zara', 'Cat', 'Sphynx', 2, 'Female', 'Healthy', 'Very affectionate and loves cuddling', 1, 'Chicago, USA', 4, 41.8781, -87.6298,
    'Sweet and gentle, loves to be petted and cuddled.',
    'Needs daily playtime and interaction. Enjoys lounging on laps and playing with feather toys.',
    'Eats premium dry cat food twice a day. Enjoys occasional wet food treats.',
    'Calm household preferred',
    'House with access to outdoors',
    'Gets along well with other cats',
    'White',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to outdoors',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Daisy', 'Dog', 'Golden Retriever', 3, 'Female', 'Healthy', 'Loves kids and is very friendly', 1, 'Miami, USA', 1, 25.7617, -80.1918,
    'Friendly and outgoing, loves to play and cuddle with children.',
    'Needs daily walks and playtime. Enjoys running in the park and playing with toys.',
    'Eats premium dry dog food twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with access to outdoors',
    'Gets along well with other dogs',
    'Golden',
    'Mare',
    1,
    '3 years',
    'Moving to a place without access to outdoors',
    1,
    'Dedicated owner who has invested in training and socialization.');

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

-- Script de populare cu animale diverse (câini, pisici, păsări, pești, reptile)
-- Folosește utilizatorii existenți cu ID-uri 1-7

-- Inserare câini suplimentari
INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Rocky', 'Dog', 'Rottweiler', 4, 'Male', 'Healthy', 'Strong and loyal guardian dog, great with families', 1, 'Seattle, WA', 1, 47.6062, -122.3321,
    'Loyal and protective, great with children and other pets.',
    'Needs daily walks and playtime. Enjoys running in the park and playing with toys.',
    'Eats premium dry dog food twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with fenced yard',
    'Prefers to be the only pet',
    'Black and tan',
    'Mare',
    1,
    '5 years',
    'Moving to a place without access to outdoors',
    1,
    'Dedicated owner who has invested in training and socialization.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Bella', 'Dog', 'Border Collie', 2, 'Female', 'Healthy', 'Highly intelligent and energetic, needs active family', 1, 'Phoenix, AZ', 2, 33.4484, -112.0740,
    'Intelligent and energetic, needs active family.',
    'Needs daily walks and playtime. Enjoys running in the park and playing with toys.',
    'Eats premium dry dog food twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with access to outdoors',
    'Prefers to be the only pet',
    'Tri-color',
    'Mare',
    1,
    '1 year',
    'Family moving to a place without access to outdoors',
    1,
    'Dedicated owner who has invested in training and socialization.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Zeus', 'Dog', 'Great Dane', 5, 'Male', 'Healthy', 'Gentle giant, loves children and other pets', 1, 'Denver, CO', 3, 39.7392, -104.9903,
    'Gentle giant, loves children and other pets.',
    'Needs daily walks and playtime. Enjoys running in the park and playing with toys.',
    'Eats premium dry dog food twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with access to outdoors',
    'Prefers to be the only pet',
    'Brindle',
    'Mare',
    1,
    '3 years',
    'Moving to a place without access to outdoors',
    1,
    'Dedicated owner who has invested in training and socialization.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Coco', 'Dog', 'Poodle', 3, 'Female', 'Healthy', 'Hypoallergenic, perfect for families with allergies', 1, 'Las Vegas, NV', 4, 36.1699, -115.1398,
    'Hypoallergenic, perfect for families with allergies.',
    'Needs daily walks and playtime. Enjoys running in the park and playing with toys.',
    'Eats premium dry dog food twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with access to outdoors',
    'Prefers to be the only pet',
    'Tri-color',
    'Mare',
    1,
    '2 years',
    'Moving to a place without access to outdoors',
    1,
    'Dedicated owner who has invested in training and socialization.');

-- Inserare pisici suplimentare
INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Shadow', 'Cat', 'Russian Blue', 4, 'Male', 'Healthy', 'Quiet and reserved, prefers calm environments', 1, 'Seattle, WA', 5, 47.6062, -122.3321,
    'Quiet and reserved, prefers calm environments.',
    'Indoor cat who enjoys window watching and playing with feather toys.',
    'Premium cat food with occasional wet food treats.',
    'Calm household preferred',
    'Indoor only',
    'Prefers to be the only pet',
    'Gray with silver tips',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to outdoors',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Princess', 'Cat', 'Ragdoll', 2, 'Female', 'Healthy', 'Docile and affectionate, loves being held', 1, 'Portland, OR', 6, 45.5152, -122.6784,
    'Docile and affectionate, loves being held.',
    'Indoor cat who enjoys window watching and playing with feather toys.',
    'Premium cat food with occasional wet food treats.',
    'Calm household preferred',
    'Indoor only',
    'Prefers to be the only pet',
    'White with grey patches',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to outdoors',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Tiger', 'Cat', 'Bengal', 3, 'Male', 'Healthy', 'Active and playful, has beautiful wild markings', 1, 'San Diego, CA', 7, 32.7157, -117.1611,
    'Active and playful, has beautiful wild markings.',
    'Needs daily playtime and interaction. Enjoys chasing laser pointers and playing with feather toys.',
    'Eats premium dry cat food twice a day. Enjoys occasional wet food treats.',
    'Active household preferred',
    'House with access to outdoors',
    'Prefers to be the only pet',
    'Spotted',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to outdoors',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

-- Inserare păsări
INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Sunny', 'Bird', 'Canary', 2, 'Male', 'Healthy', 'Beautiful singer, bright yellow color, very social', 1, 'Phoenix, AZ', 1, 33.4484, -112.0740,
    'Beautiful singer, bright yellow color, very social.',
    'Needs daily playtime and interaction. Enjoys being around other birds and people.',
    'Eats canary seed mix twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with access to outdoors',
    'Gets along well with other birds',
    'Yellow',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to outdoors',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Polly', 'Bird', 'African Grey Parrot', 8, 'Female', 'Healthy', 'Extremely intelligent, can learn many words and phrases', 1, 'Denver, CO', 2, 39.7392, -104.9903,
    'Extremely intelligent, can learn many words and phrases.',
    'Needs daily playtime and interaction. Enjoys being around other birds and people.',
    'Eats canary seed mix twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with access to outdoors',
    'Gets along well with other birds',
    'Gray with white patches',
    'Medie',
    1,
    '5 years',
    'Moving to a place without access to outdoors',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Rio', 'Bird', 'Cockatiel', 3, 'Male', 'Healthy', 'Friendly and social, loves to whistle and interact', 1, 'Seattle, WA', 3, 47.6062, -122.3321,
    'Friendly and social, loves to whistle and interact.',
    'Needs daily playtime and interaction. Enjoys being around other birds and people.',
    'Eats canary seed mix twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with access to outdoors',
    'Gets along well with other birds',
    'White with grey patches',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to outdoors',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Angel', 'Bird', 'Budgerigar', 1, 'Female', 'Healthy', 'Colorful and playful, great for beginners', 1, 'Las Vegas, NV', 4, 36.1699, -115.1398,
    'Colorful and playful, great for beginners.',
    'Needs daily playtime and interaction. Enjoys being around other birds and people.',
    'Eats canary seed mix twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with access to outdoors',
    'Gets along well with other birds',
    'Yellow with green patches',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to outdoors',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Echo', 'Bird', 'Lovebird', 2, 'Male', 'Healthy', 'Affectionate and bonds strongly with owner', 1, 'Portland, OR', 5, 45.5152, -122.6784,
    'Affectionate and bonds strongly with owner.',
    'Needs daily playtime and interaction. Enjoys being around other birds and people.',
    'Eats canary seed mix twice a day. Enjoys occasional treats during training.',
    'Active household preferred',
    'House with access to outdoors',
    'Gets along well with other birds',
    'White with grey patches',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to outdoors',
    1,
    'Caring owner who ensures regular grooming and vet check-ups.');

-- Inserare pești
INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Nemo', 'Fish', 'Clownfish', 1, 'Male', 'Healthy', 'Colorful marine fish, requires saltwater aquarium', 1, 'San Diego, CA', 6, 32.7157, -117.1611,
    'Colorful marine fish, requires saltwater aquarium.',
    'Needs saltwater aquarium with appropriate lighting and filtration.',
    'Eats marine fish food twice a day. Enjoys occasional treats during training.',
    'Aquarium household preferred',
    'Aquarium',
    'Prefers to be the only pet',
    'Orange with white stripes',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to saltwater',
    1,
    'Caring owner who ensures regular maintenance and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Goldie', 'Fish', 'Goldfish', 2, 'Female', 'Healthy', 'Classic pet fish, easy to care for, peaceful nature', 1, 'Phoenix, AZ', 7, 33.4484, -112.0740,
    'Classic pet fish, easy to care for, peaceful nature.',
    'Needs regular water changes and occasional feeding.',
    'Eats goldfish food twice a day. Enjoys occasional treats during training.',
    'Aquarium household preferred',
    'Aquarium',
    'Prefers to be the only pet',
    'Golden',
    'Medie',
    1,
    '2 years',
    'Moving to a place without access to a pond',
    1,
    'Caring owner who ensures regular maintenance and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Beta', 'Fish', 'Betta Fish', 1, 'Male', 'Healthy', 'Beautiful flowing fins, requires individual tank', 1, 'Denver, CO', 1, 39.7392, -104.9903,
    'Beautiful flowing fins, requires individual tank.',
    'Needs individual tank with appropriate filtration and lighting.',
    'Eats betta fish food twice a day. Enjoys occasional treats during training.',
    'Aquarium household preferred',
    'Aquarium',
    'Prefers to be the only pet',
    'Orange with black stripes',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to a pond',
    1,
    'Caring owner who ensures regular maintenance and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Splash', 'Fish', 'Angelfish', 3, 'Female', 'Healthy', 'Elegant tropical fish, peaceful community fish', 1, 'Seattle, WA', 2, 47.6062, -122.3321,
    'Elegant tropical fish, peaceful community fish.',
    'Needs community tank with appropriate filtration and lighting.',
    'Eats tropical fish food twice a day. Enjoys occasional treats during training.',
    'Aquarium household preferred',
    'Aquarium',
    'Prefers to be the only pet',
    'Orange with white stripes',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to a pond',
    1,
    'Caring owner who ensures regular maintenance and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Bubbles', 'Fish', 'Guppy', 1, 'Male', 'Healthy', 'Small colorful fish, great for beginners', 1, 'Las Vegas, NV', 3, 36.1699, -115.1398,
    'Small colorful fish, great for beginners.',
    'Needs community tank with appropriate filtration and lighting.',
    'Eats guppy food twice a day. Enjoys occasional treats during training.',
    'Aquarium household preferred',
    'Aquarium',
    'Prefers to be the only pet',
    'Orange with white stripes',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to a pond',
    1,
    'Caring owner who ensures regular maintenance and vet check-ups.');

-- Inserare reptile
INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Spike', 'Reptile', 'Bearded Dragon', 4, 'Male', 'Healthy', 'Docile and friendly, enjoys human interaction', 1, 'Portland, OR', 4, 45.5152, -122.6784,
    'Docile and friendly, enjoys human interaction.',
    'Needs UVB light and heat lamp for proper temperature.',
    'Eats bearded dragon food twice a day. Enjoys occasional treats during training.',
    'Aquarium household preferred',
    'Aquarium',
    'Prefers to be the only pet',
    'Green with yellow stripes',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to a terrarium',
    1,
    'Caring owner who ensures regular maintenance and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Scales', 'Reptile', 'Ball Python', 6, 'Female', 'Healthy', 'Calm and easy to handle, perfect for beginners', 1, 'San Diego, CA', 5, 32.7157, -117.1611,
    'Calm and easy to handle, perfect for beginners.',
    'Needs terrarium with appropriate humidity and temperature.',
    'Eats ball python food twice a day. Enjoys occasional treats during training.',
    'Terrarium household preferred',
    'Terrarium',
    'Prefers to be the only pet',
    'Brown with black stripes',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to a terrarium',
    1,
    'Caring owner who ensures regular maintenance and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Gecko', 'Reptile', 'Leopard Gecko', 3, 'Male', 'Healthy', 'Nocturnal and low maintenance, beautiful patterns', 1, 'Phoenix, AZ', 6, 33.4484, -112.0740,
    'Nocturnal and low maintenance, beautiful patterns.',
    'Needs terrarium with appropriate humidity and temperature.',
    'Eats leopard gecko food twice a day. Enjoys occasional treats during training.',
    'Terrarium household preferred',
    'Terrarium',
    'Prefers to be the only pet',
    'Brown with black stripes',
    'Medie',
    1,
    '1 year',
    'Moving to a place without access to a terrarium',
    1,
    'Caring owner who ensures regular maintenance and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Shelly', 'Reptile', 'Russian Tortoise', 15, 'Female', 'Healthy', 'Long-lived companion, enjoys outdoor time', 1, 'Denver, CO', 7, 39.7392, -104.9903,
    'Long-lived companion, enjoys outdoor time.',
    'Needs outdoor enclosure with appropriate vegetation and access to sunlight.',
    'Eats tortoise food twice a day. Enjoys occasional treats during training.',
    'Outdoor enclosure preferred',
    'Outdoor',
    'Prefers to be the only pet',
    'Brown with yellow stripes',
    'Medie',
    1,
    '10 years',
    'Moving to a place without access to an outdoor enclosure',
    1,
    'Caring owner who ensures regular maintenance and vet check-ups.');

INSERT INTO pets (name, species, breed, age, gender, health_status, description, available_for_adoption, adoption_address, owner_id, latitude, longitude,
    personality_description, activity_description, diet_description, household_activity, household_environment, other_pets, color, marime,
    spayed_neutered, time_at_current_home, reason_for_rehoming, flea_treatment, current_owner_description)
VALUES
('Iggy', 'Reptile', 'Green Iguana', 5, 'Male', 'Healthy', 'Large and impressive, requires experienced owner', 1, 'Seattle, WA', 1, 47.6062, -122.3321,
    'Large and impressive, requires experienced owner.',
    'Needs outdoor enclosure with appropriate vegetation and access to sunlight.',
    'Eats iguana food twice a day. Enjoys occasional treats during training.',
    'Outdoor enclosure preferred',
    'Outdoor',
    'Prefers to be the only pet',
    'Green',
    'Mare',
    1,
    '5 years',
    'Moving to a place without access to an outdoor enclosure',
    1,
    'Caring owner who ensures regular maintenance and vet check-ups.');

-- Inserare cereri de adopție pentru animalele noi (folosind pet_id estimat bazat pe ordinea inserării)
-- Presupunând că animalele existente au ID-uri 1-9, noile animale vor avea ID-uri 10-31

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(10, 1, 'pending');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(11, 3, 'approved');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(12, 5, 'pending');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(15, 1, 'pending');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(18, 3, 'approved');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(22, 5, 'pending');

INSERT INTO adoptions (pet_id, adopter_id, status)
VALUES
(25, 1, 'rejection');

-- Inserare program de hrănire pentru animalele noi
INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(10, TIMESTAMP '2025-05-31 07:30:00', 'High-protein dog food', 'Daily');

INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(13, TIMESTAMP '2025-05-31 08:00:00', 'Premium cat food', 'Daily');

INSERT INTO feeding_schedule (pet_id, time, food_description, frequency)
VALUES
(16, TIMESTAMP '2025-05-31 09:00:00', 'Canary seed mix', 'Daily');

