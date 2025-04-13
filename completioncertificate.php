<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Completion Certificate</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center flex-col p-6">

  <!-- Certificate Display -->
  <section id="certificate" class="relative w-full max-w-4xl bg-white border border-gray-300 rounded-lg shadow-lg px-12 py-16 text-center">
    
    <!-- Watermark background -->
    <div class="absolute inset-0 bg-no-repeat bg-center bg-contain opacity-10 z-0" style="background-image: url('logo.png');"></div>

    <!-- Certificate Details -->
    <div class="relative z-10">
      <img src="logo.png" alt="Company Logo" class="h-20 mx-auto mb-6" />
      <h1 class="text-4xl font-extrabold text-gray-800">Certificate of Achievement</h1>
      <p class="text-xl text-gray-600 mt-4">Presented to</p>

      <div class="mt-6">
        <span class="block text-3xl font-semibold text-gray-700 underline decoration-2">[Full Name]</span>
      </div>

      <p class="text-lg text-gray-600 mt-6">For the successful completion of</p>
      <div class="mt-4">
        <span class="block text-2xl italic text-gray-700">[Course Title]</span>
      </div>

      <p class="text-sm text-gray-500 mt-10">Date Awarded</p>
      <p id="dateDisplay" class="text-xl font-medium text-gray-800"></p>

      <div class="flex justify-between mt-16 px-12">
        <div class="text-left">
          <p class="font-semibold text-gray-700">[Mentor Name]</p>
          <p class="text-sm text-gray-500">Course Mentor</p>
        </div>
        <div class="text-right">
          <p class="font-semibold text-gray-700">THE EXPLORER</p>
          <p class="text-sm text-gray-500">Institution</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Download PDF -->
  <button id="download-btn" class="mt-10 px-6 py-3 bg-blue-600 text-white text-lg font-medium rounded-lg hover:bg-blue-700 transition">
    Save as PDF
  </button>

  <script>
    // Insert current date in formatted style
    const today = new Date();
    document.getElementById('dateDisplay').textContent = today.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });

    // Generate and download certificate PDF
    document.getElementById('download-btn').addEventListener('click', () => {
      const cert = document.getElementById('certificate');
      html2pdf().set({
        margin: 0,
        filename: 'completion_certificate.pdf',
        html2canvas: { scale: 3 },
        jsPDF: {
          unit: 'px',
          format: [cert.offsetWidth, cert.offsetHeight],
          orientation: 'portrait',
        }
      }).from(cert).save();
    });
  </script>
</body>
</html>
