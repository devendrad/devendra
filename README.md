devendra
========

 This repo contains 4 php scripts/files which I have developed as a part of my project of making an E-Learning Portal 
 for Farmers of Kerala and named tentatively as "Farmers Agricultural Inventory & Repository".
 
 I am dividing the files/scripts into two parts.
 1) Data Scraping - Technique used here to extract fruits/vegetable prices from vfpck.org.
 files: A) vfpck_data_grabber.php - Contains the data scraping technique to extract fruits/vegetable prices available in 
                                    tabular format from vfpck.org  store them in local database.
        B) vfpck_prices.php       - Contains the code to display these fruits/vegetable prices from the local database in 
                                    tabulare as well as chart format using the Rgraph tool.
 2) Image Processing and conversion - 
 Conversion of precipitation chart present in PNG format (at fallingrain.com) to digital values. The digital data can be 
 used to do weather analysis.
 files : A) image_processing.php - Contains the image processing script to digitize the precipitation chart images (PNG format)
                                   from fallingrain.com for different locations of Kerala.
         B) pchart.php           - Contains the display script to present the digital data in chart formatusing the Rgraph 
                                   tool.
