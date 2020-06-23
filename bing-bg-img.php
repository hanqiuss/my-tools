<?php
$url = 'https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1&nc=' . time() .'308&pid=hp&FORM=BEHPTB&video=1&ensearch=0';
$ret = file_get_contents($url);
$ret = json_decode($ret, true);
$imgUrl = $ret['images'][0]['url'];
if(!$imgUrl) exit(1);
$imgUrl = 'https://cn.bing.com' . $imgUrl;
$data   = file_get_contents($imgUrl);
if($data){
    if(!is_dir('img')){mkdir('img');}
    $list = glob('img\\*.jpg');
    file_put_contents('img/' . time().'.jpg', $data);
    file_put_contents('img/' . (time()+1).'.jpg', $data);

    foreach($list as $file){
        unlink($file);
    }
    
}
/*  
task import xml (need Administrator permissions)
1 rewrite the action element data
2 cmd :  schtasks /create /tn taskname /xml xxx.xml

<?xml version="1.0" encoding="UTF-16"?>
<Task version="1.4" xmlns="http://schemas.microsoft.com/windows/2004/02/mit/task">
  <Triggers>
    <CalendarTrigger>
      <StartBoundary>2020-06-23T09:00:00</StartBoundary>
      <Enabled>true</Enabled>
      <ScheduleByDay>
        <DaysInterval>1</DaysInterval>
      </ScheduleByDay>
    </CalendarTrigger>
    <LogonTrigger>
      <Enabled>true</Enabled>
    </LogonTrigger>
  </Triggers>
  <Principals>
    <Principal id="123">
      <GroupId>LOCAL SERVICE</GroupId>
      <RunLevel>LeastPrivilege</RunLevel>
    </Principal>
  </Principals>
  <Settings>
    <MultipleInstancesPolicy>IgnoreNew</MultipleInstancesPolicy>
    <DisallowStartIfOnBatteries>true</DisallowStartIfOnBatteries>
    <StopIfGoingOnBatteries>true</StopIfGoingOnBatteries>
    <AllowHardTerminate>true</AllowHardTerminate>
    <StartWhenAvailable>true</StartWhenAvailable>
    <RunOnlyIfNetworkAvailable>true</RunOnlyIfNetworkAvailable>
    <IdleSettings>
      <StopOnIdleEnd>true</StopOnIdleEnd>
      <RestartOnIdle>false</RestartOnIdle>
    </IdleSettings>
    <AllowStartOnDemand>true</AllowStartOnDemand>
    <Enabled>true</Enabled>
    <Hidden>false</Hidden>
    <RunOnlyIfIdle>false</RunOnlyIfIdle>
    <DisallowStartOnRemoteAppSession>false</DisallowStartOnRemoteAppSession>
    <UseUnifiedSchedulingEngine>true</UseUnifiedSchedulingEngine>
    <WakeToRun>false</WakeToRun>
    <ExecutionTimeLimit>PT72H</ExecutionTimeLimit>
    <Priority>7</Priority>
  </Settings>
  <Actions Context="123">
    <Exec>
      <Command>D:\env\php\php.exe</Command>
      <Arguments>img.php</Arguments>
      <WorkingDirectory>D:\backgroundimg</WorkingDirectory>
    </Exec>
  </Actions>
</Task>
*/
