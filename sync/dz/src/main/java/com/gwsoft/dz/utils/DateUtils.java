package com.gwsoft.dz.utils;

import java.sql.Timestamp;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;

public class DateUtils {
    
    public static Timestamp getTimestamp (Date date) {
        if (null == date) {
            return null ;
        }
        return new Timestamp(date.getTime()) ;
    }
    
    public static Date parseErpDate (String dateString) throws ParseException {
        Date date = null ;
        if (null == dateString || "".equals(dateString)) {
            return date ;
        }
        int dotIndex = dateString.indexOf(".") ;
        if (dotIndex > 0) {
            dateString = dateString.substring(0, dotIndex) ;
        }
        SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss") ;
        date = format.parse(dateString) ;
        return date ;
    }

}
