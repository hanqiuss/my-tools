/**
 * 统一社会信用代码验证规则
 * unified social credit identifier check
 * 参考：
 *   GB 32100-2015
 *   GB 32100-2015《法人和其他组织统一社会信用代码编码规则》国家标准第1号修改单
 *   GB 11714-1997
 * 
 * 其他：如果需要的话，可以自行增加对地区编码的校验，位于第3-8位上
 */


function check(code){
    const regx = /^((1[1239])|(2[19])|(3[123459])|(4[19])|(5[1239])|(6[129])|(7[129])|(8[19])|(9[123])|(A[19])|(N[1239])|(Y1))\d{6}[0-9A-Z]{9}[0-9A-Y]$/
    if(!regx.test(code)){return false;}
    const map = ['0','1','2','3','4','5','6','7','8','9',
                'A','B','C','D','E','F','G','H','J','K','L','M','N',
                'P','Q','R','T','U','W','X','Y']
    let checkCode = 11-code.substring(8,16).split('').map(x=>['0','1','2','3','4','5','6','7','8','9',
                'A','B','C','D','E','F','G','H','I','J','K','L','M','N',
                'O','P','Q','R','S','T','U','V','W','X','Y','Z']
                .indexOf(x))
                .reduce((a,b,i)=>a+b*([3,7,9,10,5,8,4,2][i]),0) % 11;
    checkCode = checkCode===11?'X':checkCode.toString();
    if(checkCode != code[16]){return false;}
    checkCode = 31 -  code.substring(0,17).split('')
                          .map(x=>map.indexOf(x))
                          .reduce((a,b,i)=>a+b*((3**i)%31),0) % 31;
    checkCode = map[checkCode];
    return checkCode == code[17];
}
