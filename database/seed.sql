-- =============================================
-- TRAFFIC VIOLATION LOOKUP - Seed Data
-- =============================================
USE traffic_violation_db;

-- =============================================
-- USERS (password: 123456 for all regular users)
-- Admin: admin@traffic.vn / admin123
-- =============================================
INSERT INTO users (fullname, email, phone, password, role, status) VALUES
('Administrator', 'admin@traffic.vn', '0988888888', '$2y$10$jS9lPuklBq9XFyapWKnQ5OpU7yjBDn7SvbLFnrzE2aFGNcGysAUE6', 'admin', 1),
('Nguyen Van An', 'an.nguyen@gmail.com', '0912345678', '$2y$10$CXOPLhcpoJsdSMiRjCKtsuCKU0tGbvAMh5pnjDd.qSxEDjYj1b1Uy', 'user', 1),
('Tran Thi Binh', 'binh.tran@gmail.com', '0923456789', '$2y$10$CXOPLhcpoJsdSMiRjCKtsuCKU0tGbvAMh5pnjDd.qSxEDjYj1b1Uy', 'user', 1),
('Le Van Cuong', 'cuong.le@gmail.com', '0934567890', '$2y$10$CXOPLhcpoJsdSMiRjCKtsuCKU0tGbvAMh5pnjDd.qSxEDjYj1b1Uy', 'user', 1),
('Pham Thi Dung', 'dung.pham@gmail.com', '0945678901', '$2y$10$CXOPLhcpoJsdSMiRjCKtsuCKU0tGbvAMh5pnjDd.qSxEDjYj1b1Uy', 'user', 1),
('Hoang Van Em', 'em.hoang@gmail.com', '0956789012', '$2y$10$CXOPLhcpoJsdSMiRjCKtsuCKU0tGbvAMh5pnjDd.qSxEDjYj1b1Uy', 'user', 1);

-- =============================================
-- OFFENSE CATEGORIES
-- =============================================
INSERT INTO offense_categories (id, name, description) VALUES
(1, 'Speeding Violations', 'Violations related to exceeding the prescribed speed limit'),
(2, 'Traffic Signal Violations', 'Running red lights, not obeying traffic signs, lane markings'),
(3, 'Alcohol Violations', 'Operating a vehicle with blood alcohol concentration exceeding the limit'),
(4, 'Lane Violations', 'Wrong lane, lane encroachment, reckless overtaking, driving against traffic'),
(5, 'Document Violations', 'No driving license, expired inspection, no insurance'),
(6, 'Other Violations', 'Other traffic violations');

-- =============================================
-- OFFENSES
-- =============================================
INSERT INTO offenses (id, name, description, penalty, category_id) VALUES
-- Speeding
(1, 'Speeding under 10 km/h', 'Exceeding the speed limit by less than 10 km/h', '300,000 - 400,000 VND', 1),
(2, 'Speeding 10-20 km/h', 'Exceeding the speed limit by 10 to 20 km/h', '4,000,000 - 6,000,000 VND', 1),
(3, 'Speeding over 20 km/h', 'Exceeding the speed limit by more than 20 km/h', '6,000,000 - 8,000,000 VND', 1),
-- Traffic signals
(4, 'Running red light', 'Failing to obey traffic signal lights', '4,000,000 - 6,000,000 VND', 2),
(5, 'Disobeying traffic signs', 'Not complying with traffic signs', '2,000,000 - 3,000,000 VND', 2),
(6, 'Running yellow light', 'Running a yellow light when safety is not guaranteed', '1,000,000 - 2,000,000 VND', 2),
-- Alcohol
(7, 'Alcohol level 1', 'BAC <= 50mg/100ml blood or <= 0.25mg/liter breath', '6,000,000 - 8,000,000 VND', 3),
(8, 'Alcohol level 2', 'BAC 50-80mg/100ml blood or 0.25-0.4mg/liter breath', '16,000,000 - 18,000,000 VND', 3),
-- Lane
(9, 'Wrong lane', 'Driving in the wrong lane', '4,000,000 - 6,000,000 VND', 4),
(10, 'Driving against traffic', 'Driving against the direction of a one-way road', '4,000,000 - 6,000,000 VND', 4),
(11, 'Reckless overtaking', 'Swerving into oncoming lane to overtake', '4,000,000 - 6,000,000 VND', 4),
(12, 'Illegal parking/stopping', 'Parking or stopping in a prohibited area', '800,000 - 1,000,000 VND', 4),
-- Documents
(13, 'No driving license', 'Operating a vehicle without a valid driving license', '4,000,000 - 6,000,000 VND', 5),
(14, 'Expired inspection', 'Driving a vehicle with an expired inspection certificate', '4,000,000 - 6,000,000 VND', 5),
(15, 'No insurance', 'No civil liability insurance', '400,000 - 600,000 VND', 5),
-- Other
(16, 'No helmet', 'Driver/passenger not wearing a helmet', '400,000 - 600,000 VND', 6),
(17, 'Overloading passengers', 'Carrying more passengers than allowed', '400,000 - 600,000 VND', 6),
(18, 'Using phone while driving', 'Using a handheld phone while operating a vehicle', '2,000,000 - 3,000,000 VND', 6),
(19, 'No turn signal', 'Not using turn signals when changing lanes or turning', '300,000 - 400,000 VND', 6),
(20, 'Entering prohibited road', 'Driving into a road with a no-entry sign', '2,000,000 - 3,000,000 VND', 6);

-- =============================================
-- LOCATIONS
-- =============================================
INSERT INTO locations (id, name, type, address, latitude, longitude, description) VALUES
-- Camera
(1, 'Camera - Khuat Duy Tien - Nguyen Trai Intersection', 'camera', 'Thanh Xuan, Hanoi', 20.9950, 105.8000, 'Traffic surveillance camera'),
(2, 'Camera - Pham Van Dong - Pham Hung Intersection', 'camera', 'Cau Giay, Hanoi', 21.0480, 105.7850, 'Traffic surveillance camera'),
(3, 'Camera - Giai Phong - Dai Co Viet Intersection', 'camera', 'Hai Ba Trung, Hanoi', 21.0050, 105.8450, 'Traffic surveillance camera'),
(4, 'Camera - Xa Lo Hanoi - Nguyen Van Linh Intersection', 'camera', 'District 2, HCMC', 10.7950, 106.7450, 'Traffic surveillance camera'),
(5, 'Camera - Cong Hoa - Hoang Van Thu Intersection', 'camera', 'Tan Binh, HCMC', 10.7955, 106.6550, 'Traffic surveillance camera'),
(6, 'Camera - Dragon Bridge', 'camera', 'Hai Chau, Da Nang', 16.0610, 108.2270, 'Traffic surveillance camera'),
(7, 'Camera - Hue - Dien Bien Phu Intersection', 'camera', 'Thanh Khe, Da Nang', 16.0700, 108.2150, 'Traffic surveillance camera'),
(8, 'Camera - Binh Bridge', 'camera', 'Hai Phong', 20.8540, 106.6780, 'Traffic surveillance camera'),
-- Traffic Police
(9, 'Hanoi Traffic Police Department', 'csgt', '86 Ly Tu Trong, Hoan Kiem, Hanoi', 21.0278, 105.8498, 'Hanoi Traffic Police HQ'),
(10, 'HCMC Traffic Police Department', 'csgt', '63/2/1 Hong Bang, District 11, HCMC', 10.7670, 106.6510, 'HCMC Traffic Police HQ'),
(11, 'Da Nang Traffic Police Department', 'csgt', '80 Le Loi, Hai Chau, Da Nang', 16.0680, 108.2210, 'Da Nang Traffic Police HQ'),
(12, 'Hai Phong Traffic Police Department', 'csgt', '18 Tran Hung Dao, Hong Bang, Hai Phong', 20.8620, 106.6820, 'Hai Phong Traffic Police HQ'),
-- Toll stations
(13, 'Phap Van - Cau BOT Toll Station', 'toll', 'Hoang Mai, Hanoi', 20.9450, 105.8280, 'Phap Van - Cau Gi expressway toll'),
(14, 'An Suong BOT Toll Station', 'toll', 'Hoc Mon, HCMC', 10.8320, 106.6080, 'An Suong BOT toll station'),
(15, 'Hai Van BOT Toll Station', 'toll', 'Lien Chieu, Da Nang', 16.1370, 108.1290, 'Hai Van tunnel toll station'),
-- Inspection centers
(16, 'Vehicle Inspection Center 29-01D', 'inspection', 'Hoang Mai, Hanoi', 20.9870, 105.8500, 'Motor vehicle inspection center'),
(17, 'Vehicle Inspection Center 50-01S', 'inspection', 'District 2, HCMC', 10.7890, 106.7500, 'Motor vehicle inspection center'),
(18, 'Vehicle Inspection Center 43-01S', 'inspection', 'Cam Le, Da Nang', 16.0150, 108.1850, 'Motor vehicle inspection center');

-- =============================================
-- VIOLATIONS (Sample traffic violation data)
-- =============================================
INSERT INTO violations (plate_number, vehicle_type, violation_date, location_id, offense_id, status, fine_amount, decision_number, decision_date) VALUES
-- Hanoi - cars
('30A-12345', 'car', '2026-04-15 08:30:00', 1, 4, 'pending', '5,000,000 VND', NULL, NULL),
('30A-12345', 'car', '2026-03-01 14:20:00', 2, 2, 'processed', '4,500,000 VND', 'QD-2026-00421', '2026-03-15'),
('30F-56789', 'car', '2026-04-20 09:15:00', 3, 9, 'pending', '5,000,000 VND', NULL, NULL),
('30F-56789', 'car', '2026-02-10 16:45:00', 1, 18, 'paid', '2,500,000 VND', 'QD-2026-00234', '2026-02-20'),
('29A-11111', 'car', '2026-04-25 11:00:00', 2, 1, 'pending', '400,000 VND', NULL, NULL),
('29A-22222', 'car', '2026-04-18 07:50:00', 1, 4, 'pending', '5,000,000 VND', NULL, NULL),
('29F-33333', 'car', '2026-04-10 13:30:00', 3, 5, 'processed', '2,500,000 VND', 'QD-2026-00500', '2026-04-20'),
('30E-44444', 'car', '2026-04-05 10:20:00', 2, 2, 'pending', '5,500,000 VND', NULL, NULL),
-- Hanoi - motorcycles
('29B1-12345', 'motorcycle', '2026-04-12 07:40:00', 1, 4, 'pending', '1,000,000 VND', NULL, NULL),
('29B1-67890', 'motorcycle', '2026-04-08 17:15:00', 3, 16, 'pending', '500,000 VND', NULL, NULL),
('30F1-11223', 'motorcycle', '2026-03-28 08:55:00', 2, 9, 'processed', '1,000,000 VND', 'QD-2026-00456', '2026-04-05'),
('29D1-44556', 'motorcycle', '2026-04-01 15:30:00', 1, 19, 'pending', '400,000 VND', NULL, NULL),
-- HCMC
('51F-12345', 'car', '2026-04-14 09:20:00', 4, 4, 'pending', '5,000,000 VND', NULL, NULL),
('51F-67890', 'car', '2026-03-20 14:10:00', 5, 2, 'paid', '4,500,000 VND', 'QD-2026-00300', '2026-03-28'),
('59F1-11223', 'motorcycle', '2026-04-16 16:45:00', 4, 16, 'pending', '500,000 VND', NULL, NULL),
('51F1-33445', 'motorcycle', '2026-04-22 08:30:00', 5, 4, 'pending', '1,000,000 VND', NULL, NULL),
('51A-55555', 'car', '2026-04-19 11:30:00', 4, 10, 'pending', '5,000,000 VND', NULL, NULL),
-- Da Nang
('43A-12345', 'car', '2026-04-11 14:50:00', 6, 4, 'pending', '5,000,000 VND', NULL, NULL),
('43F-67890', 'car', '2026-04-07 10:00:00', 7, 2, 'processed', '4,000,000 VND', 'QD-2026-00480', '2026-04-17'),
('43B1-11223', 'motorcycle', '2026-04-03 07:25:00', 6, 16, 'pending', '500,000 VND', NULL, NULL),
-- Hai Phong
('15A-12345', 'car', '2026-04-13 08:00:00', 8, 4, 'pending', '5,000,000 VND', NULL, NULL),
('15F-67890', 'car', '2026-03-25 15:40:00', 8, 2, 'paid', '4,500,000 VND', 'QD-2026-00350', '2026-04-01'),
-- Extra data for statistics
('30A-99999', 'car', '2026-04-01 09:00:00', 1, 4, 'pending', '5,000,000 VND', NULL, NULL),
('30A-88888', 'car', '2026-04-02 10:00:00', 2, 2, 'pending', '5,500,000 VND', NULL, NULL),
('30A-77777', 'car', '2026-04-03 11:00:00', 3, 9, 'processed', '5,000,000 VND', 'QD-2026-00400', '2026-04-10'),
('29B1-77777', 'motorcycle', '2026-01-10 08:00:00', 1, 16, 'paid', '500,000 VND', 'QD-2026-00100', '2026-01-15'),
('29B1-88888', 'motorcycle', '2026-02-15 09:30:00', 2, 4, 'paid', '1,000,000 VND', 'QD-2026-00150', '2026-02-22'),
('51F-77777', 'car', '2026-01-20 14:00:00', 4, 4, 'paid', '5,000,000 VND', 'QD-2026-00080', '2026-01-28'),
('43A-66666', 'car', '2026-03-05 16:00:00', 6, 2, 'paid', '4,500,000 VND', 'QD-2026-00200', '2026-03-10'),
('30F-66666', 'car', '2026-04-09 07:15:00', 1, 1, 'pending', '400,000 VND', NULL, NULL),
('51F-66666', 'car', '2026-04-17 12:20:00', 5, 5, 'pending', '2,500,000 VND', NULL, NULL),
('29F-55555', 'car', '2026-04-21 13:45:00', 2, 12, 'pending', '1,000,000 VND', NULL, NULL),
('30A-55555', 'car', '2026-04-23 15:00:00', 3, 2, 'pending', '5,500,000 VND', NULL, NULL),
('59F1-55555', 'motorcycle', '2026-04-24 06:30:00', 4, 4, 'pending', '1,000,000 VND', NULL, NULL);

-- =============================================
-- NEWS CATEGORIES
-- =============================================
INSERT INTO news_categories (id, name, slug) VALUES
(1, 'Traffic News', 'traffic-news'),
(2, 'Traffic Guide', 'traffic-guide'),
(3, 'Announcements', 'announcements'),
(4, 'Traffic Laws', 'traffic-laws'),
(5, 'License Plate Violations', 'license-plate-violations');

-- =============================================
-- NEWS
-- =============================================
INSERT INTO news (id, title, slug, content, category_id, author_id, status, views) VALUES
(1, 'Hanoi deploys 50 new traffic cameras in 2026',
 'hanoi-deploys-50-new-traffic-cameras-2026',
 '<p>The Hanoi Department of Transport has announced plans to install 50 new traffic surveillance cameras at key intersections across the city in 2026.</p><p>Planned locations include: Tran Duy Hung - Pham Hung intersection, Lang Ha - Huynh Thuc Khang intersection, Dai Co Viet - Bach Mai intersection, and other traffic hotspots.</p><p>According to the Department, expanding the traffic camera system aims to strengthen surveillance, detect and handle traffic violations, contributing to improved traffic awareness and reduced accidents.</p><p>Citizens can check traffic violations through the Traffic Police website or authorized mobile applications.</p>',
 1, 1, 'published', 1250),

(2, 'Guide to quick and accurate traffic violation lookup in 2026',
 'guide-to-traffic-violation-lookup-2026',
 '<p>To check traffic violations quickly and accurately, you need to prepare your license plate number and vehicle type. Here are the steps:</p><h4>Steps to check:</h4><ol><li>Enter the exact license plate number (including both numbers and letters)</li><li>Select the correct vehicle type (car, motorcycle, electric motorcycle)</li><li>Click the "Search" button and wait for results</li></ol><p>The system will return a list of violations (if any) including: time, location, violation type, fine amount, and processing status.</p>',
 2, 1, 'published', 890),

(3, 'Notice: Updated traffic violation fines per Decree 168/2024/ND-CP',
 'notice-updated-fines-decree-168',
 '<p>From January 1, 2025, Decree 168/2024/ND-CP officially takes effect with many changes to administrative penalties in the road traffic sector.</p><p>Key highlights:</p><ul><li>Significantly increased fines for running red lights: up to 6 million VND for cars</li><li>Increased alcohol violation fines: up to 40 million VND maximum</li><li>Added supplementary penalties such as temporary license suspension</li></ul>',
 3, 1, 'published', 2100),

(4, 'Top 10 most common traffic violations in Vietnam',
 'top-10-most-common-traffic-violations',
 '<p>According to statistics from the Traffic Police Department, the 10 most common traffic violations are:</p><ol><li>Running red lights</li><li>Speeding</li><li>Wrong lane driving</li><li>Not wearing a helmet (motorcycles)</li><li>No driving license</li><li>Using phone while driving</li><li>Alcohol violations</li><li>Not using turn signals</li><li>Illegal parking/stopping</li><li>Overloading passengers</li></ol><p>Citizens should be aware of these violations to avoid committing them while participating in traffic.</p>',
 1, 1, 'published', 780),

(5, 'Traffic violation fine processing procedure',
 'traffic-violation-fine-processing-procedure',
 '<p>The traffic violation fine processing procedure follows these steps:</p><h4>Step 1: Violation recorded</h4><p>Traffic cameras automatically capture images of violating vehicles.</p><h4>Step 2: Information verification</h4><p>Traffic police verify vehicle owner information through the vehicle registration system.</p><h4>Step 3: Notification sent</h4><p>Violation notices are sent to vehicle owners by mail or publicly posted.</p><h4>Step 4: Violation resolved</h4><p>Vehicle owners visit the traffic police office to resolve the violation or pay fines online through the National Public Service Portal.</p>',
 2, 1, 'published', 560),

(6, 'Most confusing traffic signs',
 'most-confusing-traffic-signs',
 '<p>Some traffic signs that often confuse road users:</p><ul><li><strong>Sign P.101 (No entry)</strong> and <strong>Sign P.102 (No overtaking)</strong>: Many people confuse these two signs.</li><li><strong>Sign P.103 (No cars)</strong> and <strong>Sign P.104 (No motorcycles)</strong>: Need to clearly distinguish which vehicle type is prohibited.</li><li><strong>Sign P.127 (Maximum speed)</strong> and <strong>Sign P.128 (Minimum speed)</strong>: Easy to confuse maximum and minimum speed.</li></ul><p>Road users need to understand the meaning of traffic signs to avoid violations.</p>',
 2, 1, 'published', 340),

(7, 'List of vehicles with traffic violations - April 2026 Hanoi',
 'vehicles-with-violations-april-2026-hanoi',
 '<p>The Hanoi Traffic Police Department has published the list of vehicles with traffic violations in April 2026. Here are some notable license plates:</p><ul><li>30A-12345: Running red light at Khuat Duy Tien - Nguyen Trai intersection</li><li>30F-56789: Wrong lane at Pham Van Dong - Pham Hung intersection</li><li>29A-11111: Speeding at Pham Van Dong intersection</li></ul><p>Vehicle owners please contact the Hanoi Traffic Police Department to resolve violations within the prescribed time limit.</p>',
 5, 1, 'published', 450),

(8, 'North-South Expressway: Construction progress and opening plans',
 'north-south-expressway-construction-progress',
 '<p>The North-South Expressway project is being accelerated with the goal of opening the entire route by 2027. Currently, many sections have been completed and put into operation.</p><p>Opened sections: Cao Bo - Mai Son, Cam Lo - La Son, Vinh Hao - Phan Thiet...</p><p>Under construction: Bai Vot - Ham Nghi, Ham Nghi - Vung Ang, Quang Ngai - Hoai Nhon...</p>',
 1, 1, 'published', 320);

-- =============================================
-- TRAFFIC SIGN GROUPS
-- =============================================
INSERT INTO traffic_sign_groups (id, name, sign_prefix, sort_order) VALUES
(1, 'Prohibitory Signs', 'P', 1),
(2, 'Warning Signs', 'W', 2),
(3, 'Mandatory Signs', 'R', 3),
(4, 'Informational Signs', 'S', 4),
(5, 'Supplementary Signs', 'S', 5);

-- =============================================
-- TRAFFIC SIGNS
-- =============================================
INSERT INTO traffic_signs (sign_code, name, group_id, description) VALUES
-- Prohibitory signs
('P.101', 'No entry', 1, 'All vehicles (motorized and non-motorized) are prohibited from traveling in both directions, except priority vehicles as prescribed.'),
('P.102', 'No overtaking', 1, 'All vehicles are prohibited from overtaking in the direction of the sign placement.'),
('P.103', 'No cars', 1, 'All motorized vehicles including 3-wheeled motor vehicles are prohibited, except priority vehicles.'),
('P.104', 'No motorcycles', 1, '2-wheeled and 3-wheeled motorcycles are prohibited, except priority vehicles.'),
('P.105', 'No cars and motorcycles', 1, 'Both cars and motorcycles are prohibited, except priority vehicles.'),
('P.106', 'No trucks', 1, 'All trucks with a load capacity of 1.5 tons or more are prohibited.'),
('P.107', 'No buses and trucks', 1, 'Buses and trucks are prohibited.'),
('P.115', 'Weight limit', 1, 'Vehicles with total weight exceeding the number on the sign are prohibited.'),
('P.123', 'No left turn', 1, 'All vehicles are prohibited from turning left at the sign location.'),
('P.124', 'No right turn', 1, 'All vehicles are prohibited from turning right at the sign location.'),
('P.125', 'No U-turn', 1, 'All vehicles are prohibited from making U-turns.'),
('P.127', 'Maximum speed limit', 1, 'All vehicles are prohibited from exceeding the speed shown on the sign.'),
('P.130', 'No stopping and parking', 1, 'All vehicles are prohibited from stopping and parking.'),
('P.131', 'No parking', 1, 'All vehicles are prohibited from parking.'),
-- Warning signs
('W.201', 'Dangerous curve', 2, 'Warning of an upcoming dangerous curve. Reduce speed.'),
('W.202', 'Consecutive curves', 2, 'Warning of multiple consecutive curves ahead.'),
('W.205', 'Intersection', 2, 'Warning of an upcoming intersection of roads of the same level.'),
('W.208', 'Intersection with priority road', 2, 'Warning of an upcoming intersection with a priority road.'),
('W.210', 'Railway crossing with barrier', 2, 'Warning of an upcoming railway crossing with a barrier.'),
('W.211', 'Railway crossing without barrier', 2, 'Warning of an upcoming railway crossing without a barrier.'),
('W.215', 'Slippery road', 2, 'Warning of an upcoming road section that may be slippery.'),
('W.221', 'Uneven road', 2, 'Warning of an upcoming road section with an uneven surface.'),
('W.224', 'Pedestrian crossing', 2, 'Warning of an upcoming pedestrian crossing area.'),
('W.225', 'Children', 2, 'Warning of a road section near a school where children often cross.'),
('W.234', 'Traffic lights', 2, 'Warning of an upcoming intersection with traffic lights.'),
-- Mandatory signs
('R.301', 'Compulsory direction', 3, 'All vehicles must follow the direction of the arrow.'),
('R.302', 'Compulsory right turn', 3, 'All vehicles must turn right.'),
('R.303', 'Compulsory left turn', 3, 'All vehicles must turn left.'),
('R.306', 'Minimum speed limit', 3, 'All vehicles must travel at the minimum speed shown on the sign.'),
('R.403', 'Non-motorized vehicle lane', 3, 'Road designated for non-motorized vehicles and pedestrians.'),
('R.412', 'Pedestrian zone', 3, 'Road or lane designated exclusively for pedestrians.'),
-- Informational signs
('S.401', 'Expressway begins', 4, 'Indicates the start of an expressway. Vehicles entering must comply with expressway rules.'),
('S.403', 'Parking area', 4, 'Indicates an authorized parking area.'),
('S.406', 'Gas station', 4, 'Indicates the location of a gas station.'),
('S.407', 'Repair shop', 4, 'Indicates the location of a vehicle repair shop.'),
('S.414', 'Emergency telephone', 4, 'Indicates the location of an emergency telephone.'),
('S.418', 'Hospital', 4, 'Indicates the direction to a hospital.'),
('S.422', 'Hotel', 4, 'Indicates the location of a hotel or motel.'),
('S.501', 'Distance sign', 5, 'Supplementary sign showing the distance from the sign to the indicated object.'),
('S.503', 'Direction sign', 5, 'Supplementary sign showing the direction of effect of the main sign.');

-- =============================================
-- FAQS
-- =============================================
INSERT INTO faqs (question, answer, category, sort_order) VALUES
('What is a traffic violation lookup?',
 '<p>A traffic violation lookup is the process of checking traffic violation information of a vehicle through its license plate number. These violations are recorded by traffic surveillance camera systems and processed later without stopping the vehicle at the time of the violation.</p>',
 'Lookup', 1),
('How do I check traffic violations?',
 '<p>Simply enter your license plate number and select the vehicle type (car, motorcycle, electric motorcycle) in the search box on the homepage or the Violation Lookup page. The system will return a list of violations (if any).</p>',
 'Lookup', 2),
('Is the lookup data accurate?',
 '<p>The data is compiled from public sources of the Traffic Police Department and the Vietnam Registry. However, for the most accurate information, you should check directly on the Traffic Police Department portal.</p>',
 'Data', 3),
('What should I do when my vehicle has a violation?',
 '<p>When you find a violation on your vehicle, you need to contact the traffic police office that issued the decision for guidance on resolution. You need to bring: vehicle registration, driving license, ID card. Or you can pay fines online through the National Public Service Portal.</p>',
 'Resolution', 4),
('What is the fine payment deadline?',
 '<p>The fine payment deadline is 10 days from the date of receiving the penalty decision. If overdue, you will have to pay additional fines and may face enforcement.</p>',
 'Resolution', 5),
('How to distinguish different types of traffic signs?',
 '<p>Vietnam traffic sign system is divided into 5 main groups: Prohibitory Signs (P - red circle), Warning Signs (W - yellow triangle), Mandatory Signs (R - blue circle), Informational Signs (S - blue square/rectangle), and Supplementary Signs (S - black/white square/rectangle). You can view details on our Traffic Signs page.</p>',
 'Signs', 6),
('How do traffic cameras work?',
 '<p>Traffic cameras are camera systems installed at key intersections and routes. When a violation is detected (running red light, speeding, etc.), the camera automatically captures the license plate and records the time and location of the violation. Data is sent to the traffic police processing center.</p>',
 'Camera', 7),
('What can I do with a registered account?',
 '<p>With a registered account, you can: manage your personal vehicle list, save search history, receive notifications of new violations (coming soon), and many other utilities.</p>',
 'Account', 8);

-- =============================================
-- VEHICLES (Sample user vehicles)
-- =============================================
INSERT INTO vehicles (user_id, plate_number, vehicle_type, brand, model) VALUES
(2, '30A-12345', 'car', 'Toyota', 'Vios 2022'),
(2, '29B1-12345', 'motorcycle', 'Honda', 'Vision 2023'),
(3, '30F-56789', 'car', 'Mazda', 'CX-5 2021'),
(4, '29A-11111', 'car', 'KIA', 'Cerato 2023'),
(4, '30F1-11223', 'motorcycle', 'Yamaha', 'Exciter 2022'),
(5, '51F-12345', 'car', 'Ford', 'Everest 2024');

-- =============================================
-- TRAFFIC ALERTS
-- =============================================
INSERT INTO traffic_alerts (title, content, alert_type, created_by, status) VALUES
('Traffic congestion at Khuat Duy Tien intersection due to accident', 'There is currently severe congestion at Khuat Duy Tien - Nguyen Trai intersection due to an accident between 2 cars. Commuters should avoid this area.', 'accident', 1, 1),
('Road construction on Nguyen Trai - Hanoi', 'From May 1 to July 31, 2026, Nguyen Trai road will undergo surface renovation. Vehicles should drive slowly and follow the guidance of traffic authorities.', 'construction', 1, 1);
