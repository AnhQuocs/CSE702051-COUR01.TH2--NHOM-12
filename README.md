<p align="center"><strong><font size="12">WEBSITE QUẢN LÝ DỰ ÁN CÁ NHÂN - MY PROJECT HUB</font></strong></p>

Website quản lý dự án cho từng user cá nhân, backend cung cấp các API để:
- Xác thực người dùng (đăng nhập, đăng xuất)
- Lấy dữ liệu dự án
- Cập nhật dự án
- Cập nhật thông tin người dùng

---

## 1. Mô hình dữ liệu

### User

| Trường    | Kiểu dữ liệu | Mô tả                       |
|-----------|--------------|-----------------------------|
| id        | String       | ID duy nhất của user        |
| username  | String       | Tên đăng nhập               |
| email     | String       | Email tài khoản             |
| password  | String       | Mật khẩu                    |

### Project
| Trường      | Kiểu dữ liệu | Mô tả                    |
|-------------|--------------|--------------------------|
| id          | String       | ID duy nhất của dự án    |
| userId      | String       | ID của user sở hữu dự án |
| title       | String       | Tiêu đề dự án            |
| description | String       | Mô tả chi tiết dự án     |
| priority    | String       | Độ ưu tiên: Thấp, cao,...|
| status      | String       | Trạng thái: hoàn thành, đang thực hiện,...|
| deadline    | Datetime     | Ngày kết thúc, hạn hoàn thành |






