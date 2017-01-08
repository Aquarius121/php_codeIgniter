#!/bin/bash

cd $(dirname $0)
cd ..
cd ..

for i in raw/mmi_contacts/search/*
do 
	php newswire.php cli mmi import_from_search_download import_file $i
done

# for i in raw/mmi_contacts/2014_11_04_bv1_*
# do 
# 	php index.php cli mmi import_from_search_download import_file $i bv1 title company_name email
# done

# for i in raw/mmi_contacts/2014_11_04_bv2_*
# do 
# 	php index.php cli mmi import_from_search_download import_file $i bv2 phone twitter media_type
# done

# for i in raw/mmi_contacts/2014_11_04_bv3_*
# do 
# 	php index.php cli mmi import_from_search_download import_file $i bv3 city country state
# done

# for i in raw/mmi_contacts/2014_11_04_bv4_*
# do 
# 	php index.php cli mmi import_from_search_download import_file $i bv4 zip beats date_updated
# done
