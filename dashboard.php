<?php
session_start();
require_once 'ser/data.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // diarahkan ke index.php (halaman login)
    exit;
}

$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Generator Sertifikat</title>
    <link rel="stylesheet" href="lay/dashboard-style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="dashboard">
        <h2>🎓 Dashboard Generator Sertifikat</h2>
        <div class="welcome">
            <p>Halo, <strong><?= htmlspecialchars($user['username']) ?></strong>!</p>
            <p>Selamat datang di dashboard generator sertifikat Anda.</p>
        </div>

        <div class="certificate-section">
            <h3>✨ Buat Sertifikat Baru</h3>
            <form id="certificateForm">
                <div class="form-group">
                    <label for="participantName">
                        <i class='bx bxs-user'></i> Nama Peserta:
                    </label>
                    <input type="text" id="participantName" name="participantName" required 
                           placeholder="Masukkan nama lengkap peserta">
                </div>
                
                <div class="form-group">
                    <label for="courseName">
                        <i class='bx bxs-book'></i> Nama Kursus/Pelatihan:
                    </label>
                    <select id="courseName" name="courseName" required>
                        <option value="">Pilih kursus...</option>
                        <option value="Web Development Bootcamp">Web Development Bootcamp</option>
                        <option value="Digital Marketing Course">Digital Marketing Course</option>
                        <option value="Data Science Fundamentals">Data Science Fundamentals</option>
                        <option value="UI/UX Design Workshop">UI/UX Design Workshop</option>
                        <option value="Mobile App Development">Mobile App Development</option>
                        <option value="Cybersecurity Essentials">Cybersecurity Essentials</option>
                        <option value="Cloud Computing Basics">Cloud Computing Basics</option>
                        <option value="pembelajaran ini">pembelajaran ini</option>
                        <option value="tugas php">tugas php</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="customCourse">
                        <i class='bx bxs-edit'></i> Atau masukkan kursus kustom:
                    </label>
                    <input type="text" id="customCourse" name="customCourse" 
                           placeholder="Masukkan nama kursus kustom (opsional)">
                </div>

                <button type="submit" class="btn">
                    <i class='bx bxs-magic-wand'></i> Generate Sertifikat
                </button>
            </form>
        </div>

        <div id="certificate" class="certificate">
            <h3>SERTIFIKAT PENGHARGAAN</h3>
            <div class="decorative-border"></div>
            
            <div class="cert-text">
                Dengan ini menyatakan bahwa
            </div>
            
            <div class="cert-name" id="certName">
                [Nama Peserta]
            </div>
            
            <div class="cert-text">
                telah berhasil menyelesaikan
            </div>
            
            <div class="cert-course" id="certCourse">
                [Nama Kursus]
            </div>
            
            <div class="cert-text">
                dengan dedikasi dan komitmen yang luar biasa
            </div>
            
            <div class="decorative-border"></div>
            
            <div class="cert-date" id="certDate">
                Diberikan pada tanggal: [Tanggal]
            </div>
            
            <div class="cert-signature">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Direktur Pelatihan</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Koordinator Program</div>
                </div>
            </div>
        </div>

        <div class="actions" id="certificateActions" style="display: none;">
            <button class="btn" onclick="printCertificate()">
                <i class='bx bxs-printer'></i> Cetak Sertifikat
            </button>
            <button class="btn" onclick="downloadCertificate()">
                <i class='bx bxs-download'></i> Download PDF
            </button>
            <button class="btn" onclick="resetForm()">
                <i class='bx bx-reset'></i> Buat Sertifikat Baru
            </button>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="logout.php" class="btn btn-logout">
                <i class='bx bx-log-out'></i> Logout
            </a>
        </div>
    </div>

    <script>
        document.getElementById('certificateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const participantName = document.getElementById('participantName').value.trim();
            const courseName = document.getElementById('courseName').value;
            const customCourse = document.getElementById('customCourse').value.trim();
            
            if (!participantName) {
                alert('Nama peserta harus diisi!');
                return;
            }
            
            let finalCourseName = courseName;
            if (customCourse) {
                finalCourseName = customCourse;
            } else if (!courseName) {
                alert('Pilih kursus atau masukkan kursus kustom!');
                return;
            }
            
            // Generate certificate
            generateCertificate(participantName, finalCourseName);
        });

        function generateCertificate(name, course) {
            // Update certificate content
            document.getElementById('certName').textContent = name;
            document.getElementById('certCourse').textContent = course;
            
            // Set current date
            const today = new Date();
            const dateString = today.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            document.getElementById('certDate').textContent = `Diberikan pada tanggal: ${dateString}`;
            
            // Show certificate and actions
            document.getElementById('certificate').style.display = 'block';
            document.getElementById('certificateActions').style.display = 'block';
            
            // Add animation class
            document.getElementById('certificate').classList.add('fade-in');
            
            // Smooth scroll to certificate
            setTimeout(() => {
                document.getElementById('certificate').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 100);
        }

        function printCertificate() {
            // Hide other elements and print only certificate
            const certificate = document.getElementById('certificate');
            const originalDisplay = document.body.style.display;
            const originalContents = document.body.innerHTML;
            
            // Create print-friendly version
            const printContent = `
                <html>
                <head>
                    <title>Sertifikat</title>
                    <style>
                        body { font-family: 'Poppins', Arial, sans-serif; margin: 0; padding: 20px; background: white; }
                        .certificate { 
                            background: white; 
                            border: 10px solid #667eea; 
                            border-radius: 20px; 
                            padding: 40px; 
                            text-align: center; 
                            position: relative; 
                            color: #333;
                            max-width: 800px;
                            margin: 0 auto;
                        }
                        .certificate::before {
                            content: '';
                            position: absolute;
                            top: 15px; left: 15px; right: 15px; bottom: 15px;
                            border: 3px solid #764ba2;
                            border-radius: 15px;
                        }
                        .certificate h3 { font-size: 2.5em; color: #667eea; margin-bottom: 20px; }
                        .cert-text { font-size: 1.2em; margin: 15px 0; line-height: 1.6; }
                        .cert-name { font-size: 2em; font-weight: bold; color: #764ba2; margin: 30px 0; text-decoration: underline; }
                        .cert-course { font-size: 1.3em; color: #667eea; font-style: italic; margin: 20px 0; }
                        .cert-date { font-size: 1em; color: #666; margin-top: 30px; }
                        .decorative-border { background: linear-gradient(45deg, #667eea, #764ba2); height: 4px; margin: 20px 0; }
                        .cert-signature { margin-top: 40px; display: flex; justify-content: space-between; }
                        .signature-box { text-align: center; flex: 1; }
                        .signature-line { border-top: 2px solid #333; margin: 20px 20px 5px 20px; }
                    </style>
                </head>
                <body>${certificate.outerHTML}</body>
                </html>
            `;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }

        function downloadCertificate() {
            // Simple implementation - in production you'd want to use a proper PDF library
            alert('Fitur download PDF sedang dalam pengembangan. Saat ini Anda dapat menggunakan fitur cetak dan memilih "Save as PDF".');
        }

        function resetForm() {
            document.getElementById('certificateForm').reset();
            document.getElementById('certificate').style.display = 'none';
            document.getElementById('certificateActions').style.display = 'none';
            document.getElementById('certificate').classList.remove('fade-in');
            
            // Scroll back to form
            document.querySelector('.certificate-section').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Add floating effect to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>