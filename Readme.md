1.Constant API
==============
	Refer Constant api for  Creating constant.

2.String-en.txt
===============
	Refer String-em.txt for creatiing Variables with data.

3.Create API
=============
	Create api for create, delete and insert data and some operation on data, Refer the Exist Api or department_api, division_api.

4.AJAX
=======
	Write Ajax in Common.js
	Example

	 $("select#division").change(function(){
        var m_division_id = $(this).children("option:selected").val();
        // alert("You have selected the division - " + m_division_id);
    	$.post("get_department_list.php",
	    {
	      division_id: m_division_id
	    },
	    function(data,status){
	      // alert("Data: " + data + "\nStatus: " + status);
	      $("select#department").html(data);
	    });
    });

5.Create Return Files for Ajax
===============================
	Create the Data Return File for ajax & Process the given data from ajax



   