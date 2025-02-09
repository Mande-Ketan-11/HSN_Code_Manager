### **PHP HSN Code Manager**  

#### **Project Description**  
The *PHP HSN Code Manager* is a web-based system that allows users to search for HSN codes and GST rates, while administrators can manage a comprehensive HSN database. The system supports **CSV import/export**, filtering options, and real-time search functionalities. With a **modern UI and dark theme**, the platform ensures efficient HSN code management and a seamless user experience.  

---

#### **Features**  
- 🌟 **Search HSN codes, product names, categories, and GST rates.**  
- 🔄 **Filter HSN codes dynamically based on category and GST rate.**  
- 📥 **Import HSN codes from CSV files (with duplicate handling).**  
- 📤 **Export HSN code data to PDF or Excel (CSV).**  
- 📝 **Admin panel for adding, editing, and deleting HSN codes.**  
- 🔐 **Secure login system with session-based authentication.**  
- 🎨 **Responsive UI with a modern dark theme.**  

---

#### **Technologies Used**  
- **Frontend**: HTML5, CSS3, Bootstrap, JavaScript  
- **Backend**: PHP (Core PHP), MySQL  
- **Libraries**: jQuery, jsPDF (for PDF export)  
- **Server**: XAMPP (Apache, MySQL)  

---

#### **Installation Instructions**  
1. **Clone the repository:**  
   ```bash
   git clone https://github.com/Mande-Ketan-11/PHP_HSN_Code_Manager.git
   ```
2. **Start XAMPP and enable Apache & MySQL.**  
3. **Import the SQL file** (`hsn_codes.sql`) into *phpMyAdmin* to set up the database.  
4. **Update database connection details** in `db_connection.php`.  
5. **Move the project folder** to the *htdocs* directory.  
6. **Access the application in your browser:**  
   ```
   http://localhost/PHP_HSN_Code_Manager/
   ```

---

#### **Usage Instructions**  
1. **Admin Login**  
   - Open `admin_login.php` and log in to the admin panel.  
   - If no admin account exists, register using `admin_register.php`.  
   
2. **Managing HSN Codes**  
   - Navigate to `Manage HSN Codes` for **editing or deleting** records.  
   - Click **"Add HSN Code"** to insert new HSN records.  
   
3. **Uploading CSV Files**  
   - Use `upload_csv.php` to **import bulk data** from CSV files.  
   - The system **ignores duplicate entries** automatically.  

4. **Exporting HSN Data**  
   - Use `export.php` to download HSN records in **PDF or Excel (CSV)** formats.  
   - Apply filters to **export specific categories or tax rates**.  

5. **Searching & Filtering**  
   - Search for **HSN codes, product names, or categories** in real time.  
   - Use **filters for GST rate and category** to refine results.  

---

#### **Future Enhancements**  
- 🔐 **Role-based authentication** (Separate admin & user roles).  
- 🏷️ **Tagging system** for better classification of HSN codes.  
- 📊 **Graphical analysis** of HSN code trends and tax data.  
- 📱 **Mobile-friendly improvements** for better user experience.  

---

#### **Credits**  
- **Developer:**  
  - 🛠 **Ketan Rajendra Mande**  
- **Frameworks & Libraries:** Bootstrap, jQuery, jsPDF  

---

#### **License**  
This project is licensed under the **MIT License**.
