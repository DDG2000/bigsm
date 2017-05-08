package com.gwsoft.dz.controller;

import java.io.IOException;
import java.io.PrintWriter;

import javax.servlet.http.HttpServletResponse;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;

import com.gwsoft.dz.Constant;
import com.gwsoft.dz.service.SynchronizeSchedule;

@Controller
public class ConfigController {
    
    @Autowired
    private SynchronizeSchedule syncSchedule ;

    @RequestMapping(Constant.API_RESET)
    public void reset (HttpServletResponse response) throws IOException {
        this.syncSchedule.reset();
        this.echoSuccess(response);
    }
    
    private void echoSuccess(HttpServletResponse response) throws IOException {
        PrintWriter out = response.getWriter() ;
        out.print("SUCCESS");
        out.flush();
        out.close();
    }
    
}
