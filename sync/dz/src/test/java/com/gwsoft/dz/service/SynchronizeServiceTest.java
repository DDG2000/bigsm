package com.gwsoft.dz.service;

import java.util.List;
import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import com.gwsoft.dz.model.TimerConfig;

@RunWith(SpringJUnit4ClassRunner.class)
@ContextConfiguration(locations = "classpath:applicationContext.xml")
public class SynchronizeServiceTest {
    
    @Autowired
    private SynchronizeService synchronizeService ;
    
    @Test
    public void test() {
        List<TimerConfig> list = this.synchronizeService.getConfigList() ;
        System.out.println(list.size());
    }

}
