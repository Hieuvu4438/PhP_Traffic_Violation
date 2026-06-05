-- =============================================
-- TRAFFIC VIOLATION LOOKUP - Seed Data
-- =============================================
USE traffic_violation_db;

-- =============================================
-- USERS (password: 123456 cho tất cả user thường)
-- Admin: admin@traffic.vn / admin123
-- =============================================
INSERT INTO users (fullname, email, phone, password, role, status) VALUES
('Administrator', 'admin@traffic.vn', '0988888888', '$2y$10$jS9lPuklBq9XFyapWKnQ5OpU7yjBDn7SvbLFnrzE2aFGNcGysAUE6', 'admin', 1),
('Nguyễn Văn An', 'an.nguyen@gmail.com', '0912345678', '$2y$10$CXOPLhcpoJsdSMiRjCKtsuCKU0tGbvAMh5pnjDd.qSxEDjYj1b1Uy', 'user', 1),
('Trần Thị Bình', 'binh.tran@gmail.com', '0923456789', '$2y$10$CXOPLhcpoJsdSMiRjCKtsuCKU0tGbvAMh5pnjDd.qSxEDjYj1b1Uy', 'user', 1),
('Lê Văn Cường', 'cuong.le@gmail.com', '0934567890', '$2y$10$CXOPLhcpoJsdSMiRjCKtsuCKU0tGbvAMh5pnjDd.qSxEDjYj1b1Uy', 'user', 1),
('Phạm Thị Dung', 'dung.pham@gmail.com', '0945678901', '$2y$10$CXOPLhcpoJsdSMiRjCKtsuCKU0tGbvAMh5pnjDd.qSxEDjYj1b1Uy', 'user', 1),
('Hoàng Văn Em', 'em.hoang@gmail.com', '0956789012', '$2y$10$CXOPLhcpoJsdSMiRjCKtsuCKU0tGbvAMh5pnjDd.qSxEDjYj1b1Uy', 'user', 1);

-- =============================================
-- OFFENSE CATEGORIES
-- =============================================
INSERT INTO offense_categories (id, name, description) VALUES
(1, 'Vi phạm tốc độ', 'Các lỗi liên quan đến chạy quá tốc độ quy định'),
(2, 'Vi phạm tín hiệu giao thông', 'Vượt đèn đỏ, không tuân thủ biển báo, vạch kẻ đường'),
(3, 'Vi phạm nồng độ cồn', 'Điều khiển phương tiện khi có nồng độ cồn vượt mức cho phép'),
(4, 'Vi phạm làn đường', 'Đi sai làn, lấn làn, vượt ẩu, đi ngược chiều'),
(5, 'Vi phạm giấy tờ', 'Không có GPLX, đăng kiểm hết hạn, không có bảo hiểm'),
(6, 'Vi phạm khác', 'Các lỗi vi phạm giao thông khác');

-- =============================================
-- OFFENSES
-- =============================================
INSERT INTO offenses (id, name, description, penalty, category_id) VALUES
-- Tốc độ
(1, 'Chạy quá tốc độ dưới 10 km/h', 'Vượt quá tốc độ quy định dưới 10 km/h', '300.000 - 400.000 VNĐ', 1),
(2, 'Chạy quá tốc độ từ 10-20 km/h', 'Vượt quá tốc độ quy định từ 10 đến 20 km/h', '4.000.000 - 6.000.000 VNĐ', 1),
(3, 'Chạy quá tốc độ trên 20 km/h', 'Vượt quá tốc độ quy định trên 20 km/h', '6.000.000 - 8.000.000 VNĐ', 1),
-- Tín hiệu
(4, 'Vượt đèn đỏ', 'Không chấp hành hiệu lệnh đèn tín hiệu giao thông', '4.000.000 - 6.000.000 VNĐ', 2),
(5, 'Không chấp hành biển báo', 'Không tuân thủ biển báo giao thông', '2.000.000 - 3.000.000 VNĐ', 2),
(6, 'Vượt đèn vàng', 'Vượt đèn vàng khi không đảm bảo an toàn', '1.000.000 - 2.000.000 VNĐ', 2),
-- Nồng độ cồn
(7, 'Nồng độ cồn vượt mức 1', 'Nồng độ cồn ≤ 50mg/100ml máu hoặc ≤ 0.25mg/lít khí thở', '6.000.000 - 8.000.000 VNĐ', 3),
(8, 'Nồng độ cồn vượt mức 2', 'Nồng độ cồn vượt 50-80mg/100ml máu hoặc 0.25-0.4mg/lít khí thở', '16.000.000 - 18.000.000 VNĐ', 3),
-- Làn đường
(9, 'Đi sai làn đường', 'Đi không đúng làn đường quy định', '4.000.000 - 6.000.000 VNĐ', 4),
(10, 'Đi ngược chiều', 'Điều khiển xe đi ngược chiều trên đường một chiều', '4.000.000 - 6.000.000 VNĐ', 4),
(11, 'Lấn làn, vượt ẩu', 'Lấn sang làn đường ngược chiều để vượt', '4.000.000 - 6.000.000 VNĐ', 4),
(12, 'Dừng đỗ sai quy định', 'Dừng, đỗ xe không đúng nơi quy định', '800.000 - 1.000.000 VNĐ', 4),
-- Giấy tờ
(13, 'Không có GPLX', 'Điều khiển phương tiện không có giấy phép lái xe', '4.000.000 - 6.000.000 VNĐ', 5),
(14, 'Đăng kiểm hết hạn', 'Sử dụng xe có đăng kiểm hết hạn', '4.000.000 - 6.000.000 VNĐ', 5),
(15, 'Không có bảo hiểm', 'Không có bảo hiểm trách nhiệm dân sự', '400.000 - 600.000 VNĐ', 5),
-- Khác
(16, 'Không đội mũ bảo hiểm', 'Người điều khiển/ngồi sau không đội mũ bảo hiểm', '400.000 - 600.000 VNĐ', 6),
(17, 'Chở quá số người quy định', 'Chở quá số người cho phép trên phương tiện', '400.000 - 600.000 VNĐ', 6),
(18, 'Sử dụng điện thoại khi lái xe', 'Dùng tay sử dụng điện thoại khi đang điều khiển xe', '2.000.000 - 3.000.000 VNĐ', 6),
(19, 'Không xi nhan khi chuyển hướng', 'Không bật tín hiệu khi chuyển làn, rẽ', '300.000 - 400.000 VNĐ', 6),
(20, 'Đi vào đường cấm', 'Điều khiển phương tiện vào đường có biển cấm', '2.000.000 - 3.000.000 VNĐ', 6);

-- =============================================
-- LOCATIONS
-- =============================================
INSERT INTO locations (id, name, type, address, latitude, longitude, description) VALUES
-- Camera
(1, 'Camera Ngã tư Khuất Duy Tiến - Nguyễn Trãi', 'camera', 'Thanh Xuân, Hà Nội', 20.9950, 105.8000, 'Camera giám sát giao thông'),
(2, 'Camera Ngã tư Phạm Văn Đồng - Phạm Hùng', 'camera', 'Cầu Giấy, Hà Nội', 21.0480, 105.7850, 'Camera giám sát giao thông'),
(3, 'Camera Ngã tư Giải Phóng - Đại Cồ Việt', 'camera', 'Hai Bà Trưng, Hà Nội', 21.0050, 105.8450, 'Camera giám sát giao thông'),
(4, 'Camera Ngã tư Xa Lộ Hà Nội - Nguyễn Văn Linh', 'camera', 'Quận 2, TP.HCM', 10.7950, 106.7450, 'Camera giám sát giao thông'),
(5, 'Camera Ngã tư Cộng Hòa - Hoàng Văn Thụ', 'camera', 'Tân Bình, TP.HCM', 10.7955, 106.6550, 'Camera giám sát giao thông'),
(6, 'Camera Cầu Rồng', 'camera', 'Hải Châu, Đà Nẵng', 16.0610, 108.2270, 'Camera giám sát giao thông'),
(7, 'Camera Ngã ba Huế - Điện Biên Phủ', 'camera', 'Thanh Khê, Đà Nẵng', 16.0700, 108.2150, 'Camera giám sát giao thông'),
(8, 'Camera Cầu Bính', 'camera', 'Hải Phòng', 20.8540, 106.6780, 'Camera giám sát giao thông'),
-- CSGT
(9, 'Phòng CSGT Công an TP Hà Nội', 'csgt', '86 Lý Tự Trọng, Hoàn Kiếm, Hà Nội', 21.0278, 105.8498, 'Trụ sở Phòng CSGT Hà Nội'),
(10, 'Phòng CSGT Công an TP Hồ Chí Minh', 'csgt', '63/2/1 Hồng Bàng, Quận 11, TP.HCM', 10.7670, 106.6510, 'Trụ sở Phòng CSGT TP.HCM'),
(11, 'Phòng CSGT Công an TP Đà Nẵng', 'csgt', '80 Lê Lợi, Hải Châu, Đà Nẵng', 16.0680, 108.2210, 'Trụ sở Phòng CSGT Đà Nẵng'),
(12, 'Phòng CSGT Công an TP Hải Phòng', 'csgt', '18 Trần Hưng Đạo, Hồng Bàng, Hải Phòng', 20.8620, 106.6820, 'Trụ sở Phòng CSGT Hải Phòng'),
-- Trạm thu phí
(13, 'Trạm thu phí BOT Pháp Vân - Cầu Giẽ', 'toll', 'Hoàng Mai, Hà Nội', 20.9450, 105.8280, 'Trạm thu phí cao tốc Pháp Vân - Cầu Giẽ'),
(14, 'Trạm thu phí BOT An Sương', 'toll', 'Hóc Môn, TP.HCM', 10.8320, 106.6080, 'Trạm thu phí BOT An Sương'),
(15, 'Trạm thu phí BOT Hải Vân', 'toll', 'Liên Chiểu, Đà Nẵng', 16.1370, 108.1290, 'Trạm thu phí hầm Hải Vân'),
-- Đăng kiểm
(16, 'Trung tâm Đăng kiểm 29-01D', 'inspection', 'Hoàng Mai, Hà Nội', 20.9870, 105.8500, 'Trung tâm đăng kiểm xe cơ giới'),
(17, 'Trung tâm Đăng kiểm 50-01S', 'inspection', 'Quận 2, TP.HCM', 10.7890, 106.7500, 'Trung tâm đăng kiểm xe cơ giới'),
(18, 'Trung tâm Đăng kiểm 43-01S', 'inspection', 'Cẩm Lệ, Đà Nẵng', 16.0150, 108.1850, 'Trung tâm đăng kiểm xe cơ giới');

-- =============================================
-- VIOLATIONS (Dữ liệu mẫu phạt nguội)
-- =============================================
INSERT INTO violations (plate_number, vehicle_type, violation_date, location_id, offense_id, status, fine_amount, decision_number, decision_date) VALUES
-- Xe Hà Nội - ô tô
('30A-12345', 'car', '2026-04-15 08:30:00', 1, 4, 'pending', '5.000.000 VNĐ', NULL, NULL),
('30A-12345', 'car', '2026-03-01 14:20:00', 2, 2, 'processed', '4.500.000 VNĐ', 'QD-2026-00421', '2026-03-15'),
('30F-56789', 'car', '2026-04-20 09:15:00', 3, 9, 'pending', '5.000.000 VNĐ', NULL, NULL),
('30F-56789', 'car', '2026-02-10 16:45:00', 1, 18, 'paid', '2.500.000 VNĐ', 'QD-2026-00234', '2026-02-20'),
('29A-11111', 'car', '2026-04-25 11:00:00', 2, 1, 'pending', '400.000 VNĐ', NULL, NULL),
('29A-22222', 'car', '2026-04-18 07:50:00', 1, 4, 'pending', '5.000.000 VNĐ', NULL, NULL),
('29F-33333', 'car', '2026-04-10 13:30:00', 3, 5, 'processed', '2.500.000 VNĐ', 'QD-2026-00500', '2026-04-20'),
('30E-44444', 'car', '2026-04-05 10:20:00', 2, 2, 'pending', '5.500.000 VNĐ', NULL, NULL),
-- Xe Hà Nội - xe máy
('29B1-12345', 'motorcycle', '2026-04-12 07:40:00', 1, 4, 'pending', '1.000.000 VNĐ', NULL, NULL),
('29B1-67890', 'motorcycle', '2026-04-08 17:15:00', 3, 16, 'pending', '500.000 VNĐ', NULL, NULL),
('30F1-11223', 'motorcycle', '2026-03-28 08:55:00', 2, 9, 'processed', '1.000.000 VNĐ', 'QD-2026-00456', '2026-04-05'),
('29D1-44556', 'motorcycle', '2026-04-01 15:30:00', 1, 19, 'pending', '400.000 VNĐ', NULL, NULL),
-- Xe TP.HCM
('51F-12345', 'car', '2026-04-14 09:20:00', 4, 4, 'pending', '5.000.000 VNĐ', NULL, NULL),
('51F-67890', 'car', '2026-03-20 14:10:00', 5, 2, 'paid', '4.500.000 VNĐ', 'QD-2026-00300', '2026-03-28'),
('59F1-11223', 'motorcycle', '2026-04-16 16:45:00', 4, 16, 'pending', '500.000 VNĐ', NULL, NULL),
('51F1-33445', 'motorcycle', '2026-04-22 08:30:00', 5, 4, 'pending', '1.000.000 VNĐ', NULL, NULL),
('51A-55555', 'car', '2026-04-19 11:30:00', 4, 10, 'pending', '5.000.000 VNĐ', NULL, NULL),
-- Xe Đà Nẵng
('43A-12345', 'car', '2026-04-11 14:50:00', 6, 4, 'pending', '5.000.000 VNĐ', NULL, NULL),
('43F-67890', 'car', '2026-04-07 10:00:00', 7, 2, 'processed', '4.000.000 VNĐ', 'QD-2026-00480', '2026-04-17'),
('43B1-11223', 'motorcycle', '2026-04-03 07:25:00', 6, 16, 'pending', '500.000 VNĐ', NULL, NULL),
-- Xe Hải Phòng
('15A-12345', 'car', '2026-04-13 08:00:00', 8, 4, 'pending', '5.000.000 VNĐ', NULL, NULL),
('15F-67890', 'car', '2026-03-25 15:40:00', 8, 2, 'paid', '4.500.000 VNĐ', 'QD-2026-00350', '2026-04-01'),
-- Thêm dữ liệu cho thống kê
('30A-99999', 'car', '2026-04-01 09:00:00', 1, 4, 'pending', '5.000.000 VNĐ', NULL, NULL),
('30A-88888', 'car', '2026-04-02 10:00:00', 2, 2, 'pending', '5.500.000 VNĐ', NULL, NULL),
('30A-77777', 'car', '2026-04-03 11:00:00', 3, 9, 'processed', '5.000.000 VNĐ', 'QD-2026-00400', '2026-04-10'),
('29B1-77777', 'motorcycle', '2026-01-10 08:00:00', 1, 16, 'paid', '500.000 VNĐ', 'QD-2026-00100', '2026-01-15'),
('29B1-88888', 'motorcycle', '2026-02-15 09:30:00', 2, 4, 'paid', '1.000.000 VNĐ', 'QD-2026-00150', '2026-02-22'),
('51F-77777', 'car', '2026-01-20 14:00:00', 4, 4, 'paid', '5.000.000 VNĐ', 'QD-2026-00080', '2026-01-28'),
('43A-66666', 'car', '2026-03-05 16:00:00', 6, 2, 'paid', '4.500.000 VNĐ', 'QD-2026-00200', '2026-03-10'),
('30F-66666', 'car', '2026-04-09 07:15:00', 1, 1, 'pending', '400.000 VNĐ', NULL, NULL),
('51F-66666', 'car', '2026-04-17 12:20:00', 5, 5, 'pending', '2.500.000 VNĐ', NULL, NULL),
('29F-55555', 'car', '2026-04-21 13:45:00', 2, 12, 'pending', '1.000.000 VNĐ', NULL, NULL),
('30A-55555', 'car', '2026-04-23 15:00:00', 3, 2, 'pending', '5.500.000 VNĐ', NULL, NULL),
('59F1-55555', 'motorcycle', '2026-04-24 06:30:00', 4, 4, 'pending', '1.000.000 VNĐ', NULL, NULL);

-- =============================================
-- NEWS CATEGORIES
-- =============================================
INSERT INTO news_categories (id, name, slug) VALUES
(1, 'Tin tức giao thông', 'tin-tuc'),
(2, 'Giải đáp giao thông', 'giai-dap'),
(3, 'Thông báo', 'thong-bao'),
(4, 'Luật giao thông', 'luat-giao-thong'),
(5, 'Biển số xe bị phạt nguội', 'bien-so-xe-bi-phat-nguoi');

-- =============================================
-- NEWS
-- =============================================
INSERT INTO news (id, title, slug, content, category_id, author_id, status, views) VALUES
(1, 'Hà Nội triển khai thêm 50 camera phạt nguội mới trong năm 2026',
 'ha-noi-trien-khai-them-50-camera-phat-nguoi-moi-2026',
 '<p>Sở Giao thông Vận tải Hà Nội vừa công bố kế hoạch lắp đặt thêm 50 camera giám sát giao thông tại các nút giao thông trọng điểm trên địa bàn thành phố trong năm 2026.</p><p>Các vị trí dự kiến lắp đặt bao gồm: Ngã tư Trần Duy Hưng - Phạm Hùng, Ngã tư Láng Hạ - Huỳnh Thúc Kháng, Ngã tư Đại Cồ Việt - Bạch Mai, và nhiều điểm nóng giao thông khác.</p><p>Theo đại diện Sở GTVT, việc mở rộng hệ thống camera phạt nguội nhằm tăng cường giám sát, phát hiện và xử lý vi phạm giao thông, góp phần nâng cao ý thức người tham gia giao thông và giảm thiểu tai nạn.</p><p>Người dân có thể tra cứu phạt nguội thông qua website của Cục CSGT hoặc các ứng dụng di động được cấp phép.</p>',
 1, 1, 'published', 1250),

(2, 'Hướng dẫn tra cứu phạt nguội nhanh chóng và chính xác nhất 2026',
 'huong-dan-tra-cuu-phat-nguoi-2026',
 '<p>Để tra cứu phạt nguội một cách nhanh chóng và chính xác, bạn cần chuẩn bị biển số xe và loại phương tiện. Sau đây là các bước thực hiện:</p><h4>Các bước tra cứu:</h4><ol><li>Nhập chính xác biển số xe (bao gồm cả số và chữ)</li><li>Chọn đúng loại phương tiện (ô tô, xe máy, xe máy điện)</li><li>Nhấn nút "Tra cứu" và đợi kết quả</li></ol><p>Hệ thống sẽ trả về danh sách các vi phạm (nếu có) bao gồm: thời gian, địa điểm, hành vi vi phạm, mức phạt và trạng thái xử lý.</p>',
 2, 1, 'published', 890),

(3, 'Thông báo: Cập nhật mức phạt vi phạm giao thông theo Nghị định 168/2024/NĐ-CP',
 'thong-bao-cap-nhat-muc-phat-theo-nghi-dinh-168',
 '<p>Từ ngày 01/01/2025, Nghị định 168/2024/NĐ-CP chính thức có hiệu lực với nhiều thay đổi về mức xử phạt vi phạm hành chính trong lĩnh vực giao thông đường bộ.</p><p>Một số điểm đáng chú ý:</p><ul><li>Tăng mạnh mức phạt đối với hành vi vượt đèn đỏ: lên đến 6 triệu đồng đối với ô tô</li><li>Tăng mức phạt nồng độ cồn: mức cao nhất lên đến 40 triệu đồng</li><li>Bổ sung hình phạt bổ sung như tước GPLX có thời hạn</li></ul>',
 3, 1, 'published', 2100),

(4, 'Top 10 lỗi vi phạm giao thông phổ biến nhất tại Việt Nam',
 'top-10-loi-vi-pham-giao-thong-pho-bien-nhat',
 '<p>Theo thống kê từ Cục CSGT, 10 lỗi vi phạm giao thông phổ biến nhất bao gồm:</p><ol><li>Vượt đèn đỏ</li><li>Chạy quá tốc độ</li><li>Đi sai làn đường</li><li>Không đội mũ bảo hiểm (xe máy)</li><li>Không có GPLX</li><li>Sử dụng điện thoại khi lái xe</li><li>Nồng độ cồn vượt mức</li><li>Không xi nhan khi chuyển hướng</li><li>Dừng đỗ sai quy định</li><li>Chở quá số người quy định</li></ol><p>Người dân cần nắm rõ các lỗi vi phạm để tránh mắc phải khi tham gia giao thông.</p>',
 1, 1, 'published', 780),

(5, 'Quy trình xử lý phạt nguội đối với xe vi phạm giao thông',
 'quy-trinh-xu-ly-phat-nguoi-xe-vi-pham',
 '<p>Quy trình xử lý phạt nguội được thực hiện theo các bước sau:</p><h4>Bước 1: Ghi nhận vi phạm</h4><p>Hệ thống camera giao thông tự động ghi lại hình ảnh phương tiện vi phạm.</p><h4>Bước 2: Xác minh thông tin</h4><p>Cơ quan CSGT xác minh thông tin chủ phương tiện qua hệ thống đăng ký xe.</p><h4>Bước 3: Gửi thông báo</h4><p>Thông báo vi phạm được gửi đến chủ phương tiện qua đường bưu điện hoặc niêm yết công khai.</p><h4>Bước 4: Xử lý vi phạm</h4><p>Chủ phương tiện đến cơ quan CSGT để giải quyết hoặc nộp phạt online qua Cổng Dịch vụ công Quốc gia.</p>',
 2, 1, 'published', 560),

(6, 'Những biển báo giao thông dễ gây nhầm lẫn nhất',
 'nhung-bien-bao-giao-thong-de-gay-nham-lan',
 '<p>Một số biển báo giao thông thường gây nhầm lẫn cho người tham gia giao thông:</p><ul><li><strong>Biển P.101 (Đường cấm)</strong> và <strong>Biển P.102 (Cấm đi ngược chiều)</strong>: Nhiều người nhầm lẫn hai biển này.</li><li><strong>Biển P.103 (Cấm ô tô)</strong> và <strong>Biển P.104 (Cấm xe máy)</strong>: Cần phân biệt rõ loại phương tiện bị cấm.</li><li><strong>Biển P.127 (Tốc độ tối đa)</strong> và <strong>Biển P.128 (Tốc độ tối thiểu)</strong>: Dễ nhầm giữa tốc độ tối đa và tối thiểu.</li></ul><p>Người tham gia giao thông cần nắm vững ý nghĩa các biển báo để tránh vi phạm.</p>',
 2, 1, 'published', 340),

(7, 'Danh sách biển số xe bị phạt nguội tháng 4/2026 tại Hà Nội',
 'danh-sach-bien-so-xe-bi-phat-nguoi-thang-4-2026-ha-noi',
 '<p>Cục CSGT Hà Nội vừa công bố danh sách các phương tiện bị phạt nguội trong tháng 4/2026. Dưới đây là một số biển số tiêu biểu:</p><ul><li>30A-12345: Vượt đèn đỏ tại Ngã tư Khuất Duy Tiến - Nguyễn Trãi</li><li>30F-56789: Đi sai làn đường tại Ngã tư Phạm Văn Đồng - Phạm Hùng</li><li>29A-11111: Chạy quá tốc độ tại Ngã tư Phạm Văn Đồng</li></ul><p>Chủ phương tiện vui lòng liên hệ Phòng CSGT Hà Nội để giải quyết vi phạm trong thời hạn quy định.</p>',
 5, 1, 'published', 450),

(8, 'Cao tốc Bắc - Nam: Tiến độ thi công và kế hoạch thông xe các đoạn tuyến',
 'cao-toc-bac-nam-tien-do-thi-cong-ke-hoach-thong-xe',
 '<p>Dự án cao tốc Bắc - Nam phía Đông đang được đẩy nhanh tiến độ với mục tiêu thông xe toàn tuyến vào năm 2027. Hiện tại, nhiều đoạn tuyến đã hoàn thành và đưa vào sử dụng.</p><p>Các đoạn đã thông xe: Cao Bồ - Mai Sơn, Cam Lộ - La Sơn, Vĩnh Hảo - Phan Thiết...</p><p>Các đoạn đang thi công: Bài Vọt - Hàm Nghi, Hàm Nghi - Vũng Áng, Quảng Ngãi - Hoài Nhơn...</p>',
 1, 1, 'published', 320);

-- =============================================
-- TRAFFIC SIGN GROUPS
-- =============================================
INSERT INTO traffic_sign_groups (id, name, sign_prefix, sort_order) VALUES
(1, 'Biển báo cấm', 'P', 1),
(2, 'Biển báo nguy hiểm', 'W', 2),
(3, 'Biển hiệu lệnh', 'R', 3),
(4, 'Biển chỉ dẫn', 'S', 4),
(5, 'Biển phụ', 'S', 5);

-- =============================================
-- TRAFFIC SIGNS
-- =============================================
INSERT INTO traffic_signs (sign_code, name, group_id, description) VALUES
-- Biển báo cấm
('P.101', 'Đường cấm', 1, 'Đường cấm tất cả các loại phương tiện (cơ giới và thô sơ) đi lại cả hai hướng, trừ các xe được ưu tiên theo quy định.'),
('P.102', 'Cấm đi ngược chiều', 1, 'Cấm các loại phương tiện đi vào theo chiều đặt biển, trừ các xe được ưu tiên theo quy định.'),
('P.103', 'Cấm ô tô', 1, 'Cấm tất cả các loại xe cơ giới kể cả xe 3 bánh có động cơ đi lại, trừ xe ưu tiên.'),
('P.104', 'Cấm xe máy', 1, 'Cấm xe máy 2 bánh, 3 bánh đi lại, trừ các xe được ưu tiên.'),
('P.105', 'Cấm ô tô và xe máy', 1, 'Cấm cả ô tô và xe máy đi lại, trừ các xe được ưu tiên.'),
('P.106', 'Cấm xe tải', 1, 'Cấm tất cả các loại xe tải có trọng tải từ 1,5 tấn trở lên.'),
('P.107', 'Cấm xe khách và xe tải', 1, 'Cấm xe khách và xe tải đi lại.'),
('P.115', 'Hạn chế tải trọng xe', 1, 'Cấm các xe có tổng trọng tải vượt quá trị số ghi trên biển.'),
('P.123', 'Cấm rẽ trái', 1, 'Cấm các loại phương tiện rẽ trái tại nơi đặt biển.'),
('P.124', 'Cấm rẽ phải', 1, 'Cấm các loại phương tiện rẽ phải tại nơi đặt biển.'),
('P.125', 'Cấm quay đầu xe', 1, 'Cấm các loại phương tiện quay đầu xe.'),
('P.127', 'Tốc độ tối đa cho phép', 1, 'Cấm các loại xe chạy vượt quá tốc độ ghi trên biển.'),
('P.130', 'Cấm dừng xe và đỗ xe', 1, 'Cấm các loại phương tiện dừng và đỗ xe.'),
('P.131', 'Cấm đỗ xe', 1, 'Cấm các loại phương tiện đỗ xe.'),
-- Biển báo nguy hiểm
('W.201', 'Chỗ ngoặt nguy hiểm', 2, 'Báo hiệu sắp đến một chỗ ngoặt nguy hiểm. Tốc độ cần giảm xuống.'),
('W.202', 'Nhiều chỗ ngoặt liên tiếp', 2, 'Báo hiệu sắp đến nhiều chỗ ngoặt liên tiếp.'),
('W.205', 'Đường giao nhau', 2, 'Báo hiệu sắp đến nơi giao nhau của các đường cùng cấp.'),
('W.208', 'Giao nhau với đường ưu tiên', 2, 'Báo hiệu sắp đến nơi giao nhau với đường ưu tiên.'),
('W.210', 'Giao nhau với đường sắt có rào chắn', 2, 'Báo hiệu sắp đến chỗ giao nhau đường bộ và đường sắt có rào chắn.'),
('W.211', 'Giao nhau với đường sắt không rào chắn', 2, 'Báo hiệu sắp đến chỗ giao nhau đường bộ và đường sắt không có rào chắn.'),
('W.215', 'Đường trơn', 2, 'Báo hiệu sắp tới đoạn đường có thể xảy ra trơn trượt.'),
('W.221', 'Đường không bằng phẳng', 2, 'Báo hiệu sắp tới đoạn đường có mặt đường không bằng phẳng.'),
('W.224', 'Người đi bộ cắt ngang', 2, 'Báo hiệu sắp tới phần đường dành cho người đi bộ cắt ngang.'),
('W.225', 'Trẻ em', 2, 'Báo hiệu sắp đến đoạn đường gần trường học, nơi trẻ em thường qua đường.'),
('W.234', 'Đèn tín hiệu giao thông', 2, 'Báo trước nơi giao nhau có đèn tín hiệu giao thông.'),
-- Biển hiệu lệnh
('R.301', 'Hướng đi phải theo', 3, 'Các loại phương tiện phải đi theo hướng mũi tên chỉ.'),
('R.302', 'Hướng rẽ phải theo', 3, 'Các loại phương tiện phải rẽ phải.'),
('R.303', 'Hướng rẽ trái theo', 3, 'Các loại phương tiện phải rẽ trái.'),
('R.306', 'Tốc độ tối thiểu cho phép', 3, 'Các loại xe phải chạy với tốc độ tối thiểu bằng trị số trên biển.'),
('R.403', 'Đường dành cho xe thô sơ', 3, 'Báo hiệu đường dành riêng cho xe thô sơ và người đi bộ.'),
('R.412', 'Đường dành cho người đi bộ', 3, 'Báo hiệu đường hoặc làn đường dành riêng cho người đi bộ.'),
-- Biển chỉ dẫn
('S.401', 'Bắt đầu đường cao tốc', 4, 'Chỉ dẫn bắt đầu đường cao tốc. Các phương tiện đi vào phải tuân thủ luật đường cao tốc.'),
('S.403', 'Nơi đỗ xe', 4, 'Chỉ dẫn nơi được phép đỗ xe.'),
('S.406', 'Trạm xăng', 4, 'Chỉ dẫn vị trí trạm xăng dầu.'),
('S.407', 'Trạm sửa chữa xe', 4, 'Chỉ dẫn vị trí trạm sửa chữa ô tô.'),
('S.414', 'Điện thoại khẩn cấp', 4, 'Chỉ dẫn vị trí điện thoại dùng trong trường hợp khẩn cấp.'),
('S.418', 'Bệnh viện', 4, 'Chỉ dẫn hướng đi tới bệnh viện.'),
('S.422', 'Khách sạn', 4, 'Chỉ dẫn vị trí khách sạn, nhà nghỉ.'),
('S.501', 'Biển phụ khoảng cách', 5, 'Biển phụ chỉ khoảng cách từ vị trí đặt biển đến đối tượng báo hiệu.'),
('S.503', 'Biển phụ hướng tác dụng', 5, 'Biển phụ chỉ hướng tác dụng của biển chính.');

-- =============================================
-- FAQS
-- =============================================
INSERT INTO faqs (question, answer, category, sort_order) VALUES
('Tra cứu phạt nguội là gì?',
 '<p>Tra cứu phạt nguội là việc kiểm tra thông tin vi phạm giao thông của phương tiện thông qua biển số xe. Các vi phạm này được ghi nhận bởi hệ thống camera giám sát giao thông và được xử lý sau đó mà không cần dừng xe tại thời điểm vi phạm.</p>',
 'Tra cứu', 1),
('Làm thế nào để tra cứu phạt nguội?',
 '<p>Bạn chỉ cần nhập biển số xe và chọn loại phương tiện (ô tô, xe máy, xe máy điện) vào ô tra cứu trên trang chủ hoặc trang Tra Cứu Phạt Nguội. Hệ thống sẽ trả về danh sách các vi phạm (nếu có).</p>',
 'Tra cứu', 2),
('Dữ liệu tra cứu có chính xác không?',
 '<p>Dữ liệu được tổng hợp từ các nguồn công khai của Cục CSGT và Cục Đăng Kiểm Việt Nam. Tuy nhiên, để có thông tin chính xác nhất, bạn nên kiểm tra trực tiếp trên cổng thông tin của Cục CSGT.</p>',
 'Dữ liệu', 3),
('Tôi cần làm gì khi xe bị phạt nguội?',
 '<p>Khi phát hiện xe bị phạt nguội, bạn cần liên hệ với cơ quan CSGT nơi ra quyết định xử phạt để được hướng dẫn giải quyết. Bạn cần mang theo: giấy tờ xe, GPLX, CMND/CCCD. Hoặc bạn có thể nộp phạt online qua Cổng Dịch vụ công Quốc gia.</p>',
 'Xử lý', 4),
('Thời hạn nộp phạt là bao lâu?',
 '<p>Thời hạn nộp phạt là 10 ngày kể từ ngày nhận được quyết định xử phạt. Nếu quá hạn, bạn sẽ phải nộp thêm tiền phạt và có thể bị cưỡng chế thi hành.</p>',
 'Xử lý', 5),
('Làm sao để phân biệt các loại biển báo giao thông?',
 '<p>Hệ thống biển báo giao thông Việt Nam được chia thành 5 nhóm chính: Biển báo cấm (P - hình tròn viền đỏ), Biển báo nguy hiểm (W - hình tam giác vàng), Biển hiệu lệnh (R - hình tròn xanh), Biển chỉ dẫn (S - hình vuông/chữ nhật xanh), và Biển phụ (S - hình vuông/chữ nhật trắng đen). Bạn có thể xem chi tiết tại trang Biển báo của chúng tôi.</p>',
 'Biển báo', 6),
('Camera phạt nguội hoạt động như thế nào?',
 '<p>Camera phạt nguội là hệ thống camera được lắp đặt tại các nút giao thông, tuyến đường trọng điểm. Khi phát hiện phương tiện vi phạm (vượt đèn đỏ, chạy quá tốc độ,...), camera sẽ tự động chụp ảnh biển số xe và ghi nhận thời gian, địa điểm vi phạm. Dữ liệu được gửi về trung tâm xử lý của CSGT.</p>',
 'Camera', 7),
('Tôi có thể đăng ký tài khoản để làm gì?',
 '<p>Khi đăng ký tài khoản, bạn có thể: quản lý danh sách phương tiện cá nhân, lưu lịch sử tra cứu, nhận thông báo khi có vi phạm mới (tính năng sắp triển khai), và nhiều tiện ích khác.</p>',
 'Tài khoản', 8);

-- =============================================
-- VEHICLES (Phương tiện mẫu của users)
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
('Ùn tắc giao thông tại Ngã tư Khuất Duy Tiến do tai nạn', 'Hiện đang có ùn tắc nghiêm trọng tại khu vực Ngã tư Khuất Duy Tiến - Nguyễn Trãi do tai nạn giữa 2 xe ô tô. Người dân nên tránh khu vực này.', 'accident', 1, 1),
('Thi công đường Nguyễn Trãi - Hà Nội', 'Từ ngày 01/05/2026 đến 31/07/2026, đường Nguyễn Trãi sẽ được thi công cải tạo mặt đường. Các phương tiện lưu thông chú ý đi chậm và theo hướng dẫn của lực lượng chức năng.', 'construction', 1, 1);
