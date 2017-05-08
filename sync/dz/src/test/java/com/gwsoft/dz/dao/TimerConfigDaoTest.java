package com.gwsoft.dz.dao;

import java.util.List;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;

import com.gwsoft.dz.model.TimerConfig;

@RunWith(SpringJUnit4ClassRunner.class)
@ContextConfiguration(locations = "classpath:applicationContext.xml")
public class TimerConfigDaoTest {
    
    @Autowired
    private TimerConfigDao timerConfigDao ;
    
    @Test
    public void test() {
        List<TimerConfig> list = this.timerConfigDao.getConfigList() ;
        System.out.println(list.size());
    }

}
