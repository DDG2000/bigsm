package com.gwsoft.dz.service;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;

import com.gwsoft.dz.model.Bill;
import com.gwsoft.dz.model.Employee;
import com.gwsoft.dz.model.Goods;
import com.gwsoft.dz.model.Place;
import com.gwsoft.dz.model.PurchaseProof;

@RunWith(SpringJUnit4ClassRunner.class)
@ContextConfiguration(locations = "classpath:applicationContext.xml")
public class SynchronizeTaskTest {
    
    @Autowired
    private SynchronizeTask<Goods> goodsSyncTask ;
    
    @Autowired
    private SynchronizeTask<Employee> employeeSyncTask ;
    
    @Autowired
    private SynchronizeTask<Place> placeSyncTask ;

    @Autowired
    private SynchronizeTask<Place> companySyncTask ;
    
    @Autowired
    private SynchronizeTask<PurchaseProof> purchaseProofSyncTask ;
    
    @Autowired
    private SynchronizeTask<Bill> billSyncTask ;
    
    @Autowired
    private SynchronizeTask<Bill> projectSyncTask ;
    
    @Test
    public void testGoodsSync() {
        goodsSyncTask.run();
    }
    
    @Test
    public void testEmployeeSync() {
        employeeSyncTask.run();
    }
    
    @Test
    public void testPlaceSync() {
        placeSyncTask.run();
    }
    
    @Test
    public void testCompanySync() {
        companySyncTask.run() ;
    }
    
    @Test
    public void testPurchaseProofSync() {
        purchaseProofSyncTask.run();
    }
    
    @Test
    public void testBillSync() {
        billSyncTask.run() ;
    }
    
    @Test
    public void testProjectSync() {
        projectSyncTask.run();
    }
    
}
