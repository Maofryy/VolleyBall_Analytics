from numpy.lib.function_base import extract
import extraction
from extraction import extract_pdf, classes
import unittest
import json
import os

class TestClass(unittest.TestCase):

    def test_translate(self):
        str_fr = "Janvier Février Mars Avril Mai Juin Juillet Aout Août a à"
        str_en = "January February March April May June July Aout August a at"
        result = extract_pdf.translate_month(str_fr)
        self.assertEqual(result, str_en)

    def test_wrong_format(self):
        self.assertRaises(classes.FormatInvalidError, extract_pdf.extract_pdf, "format_test.pdf")

    def test_empty_format(self):
        file = os.path.join(os.path.dirname(__file__), "test_data/"+"empty_test.pdf")
        output = os.path.join(os.path.dirname(__file__), "test_data/"+"test_out.json")
        ref = os.path.join(os.path.dirname(__file__), "test_data/"+"empty_test.json")
        extract_pdf.extract_match(file, output)
        with open(ref) as f:
            ref = json.load(f)
        with open(output) as f:
            out = json.load(f)
        os.remove(output)
        self.assertEqual(ref, out)

    def test_sample_format(self):
        file = os.path.join(os.path.dirname(__file__), "test_data/"+"sample_test.pdf")
        output = os.path.join(os.path.dirname(__file__), "test_data/"+"test_out.json")
        ref = os.path.join(os.path.dirname(__file__), "test_data/"+"sample_test.json")
        extract_pdf.extract_match(file, output)
        with open(ref) as f:
            ref = json.load(f)
        with open(output) as f:
            out = json.load(f)
        os.remove(output)
        self.assertEqual(ref, out)




if __name__ == '__main__':
    unittest.main()
# Run in root folder : python -m unittest extraction.test
