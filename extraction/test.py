import extraction
from extraction import extract_pdf
import unittest
import json

class TestClass(unittest.TestCase):

    def test_translate(self):
        str_fr = "Janvier Février Mars Avril Mai Juin Juillet Aout Août a à"
        str_en = "January February March April May June July Aout August a at"
        result = extract_pdf.translate_month(str_fr)
        self.assertEqual(result, str_en)

if __name__ == '__main__':
    unittest.main()
# Run in root folder : python -m unittest extraction.test
