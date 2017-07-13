<?
//회원가입 항목 HTML
function getJoinHtml($param) {

    $html = <<<HTML
        <h4>$param[member_dvs] 회원 가입 정보</h4>
        <table class="line input">
            <colgroup>
                <col width="130">
                <col>
                <col width="130">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th>아이디</th>
                    <td colspan="3">
                        <input type="text" id="member_id" name="member_id" maxlength="20">
                        <button type="button" onclick="getIdOver();">아이디 중복확인</button>
                        <span class="note">8~20자의 영문 소문자, 숫자와 특수기호(_),(-)만 사용 사능합니다.</span>
                    </td>
                </tr>
                <tr>
                    <th>비밀번호</th>
                    <td colspan="3">
                        <input type="password" id="passwd" name="passwd" maxlength="20">
                        <span class="note">8~20자의 영문 대소문자, 숫자, 특수문자를 사용하세요.</span>
                    </td>
                </tr>
                <tr>
                    <th>비밀번호 확인</th>
                    <td colspan="3"><input type="password" id="passwd_re" name="passwd_re"></td>
                </tr>
                $param[etprs_html]
                <tr>
                    <th>$param[member_name]</th>
                    <td><input type="text" id="$param[member_name_val]" name="$param[member_name_val]"></td>
                    <th>생년월일</th>
                    <td class="date">
                      <input type="text" class="year" id="birth_year" name="birth_year" maxlength="4">
                      <select id="birth_month" name="birth_month">
                        $param[month_html]
                      </select>
                      <select id="birth_day" name="birth_day">
                        $param[day_html]
                      </select>
                    </td>
                </tr>
                $param[tob_html]
                <tr>
                    <th>이메일</th>
                    <td colspan="3" class="email _replyToEmail">
                        <input type="text" id="email_addr" name="email_addr">
                        <span class="symbol">@</span>
                        <input type="text" class="_domain" id="email_domain" name="email_domain">
                        <select>
                            <option class="_custom" value="">직접입력</option>
                            $param[email_html]
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>전화번호</th>
                    <td class="telNum">
                        <select id="tel_num1" name="tel_num1">
                            $param[tel_html]
                        </select>
                        <input type="text" id="tel_num2" name="tel_num2" maxlength="4">
                        <input type="text" id="tel_num3" name="tel_num3" maxlength="4">
                    </td>
                    <th>휴대전화</th>
                    <td class="telNum">
                        <select id="cel_num1" name="cel_num1">
                            $param[cel_html]
                        </select>
                        <input type="text" id="cel_num2" name="cel_num2" maxlength="4">
                        <input type="text" id="cel_num3" name="cel_num3" maxlength="4">
                    </td>
                </tr>
                <tr>
                    <th>주소</th>
                    <td colspan="3" class="address">
                        <div class="rowWrap postNum">
                            <input type="text" id="zipcode" name="zipcode" readonly>
                            <button type="button" onclick="getPostcode();">우편번호 찾기</button>
                        </div>
                        <div class="rowWrap">
                            <input type="text" class="address" id="addr" name="addr" readonly>
                            <input type="text" class="address" id="addr_detail" name="addr_detail">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>이메일 수신 여부</th>
                    <td colspan="3">
                        <span class="description">뉴스레터, 이벤트 안내 등의 정보를 수신합니다.</span>
                        <label><input type="radio" name="mailing_yn" value="Y"> 예</label>
                        <label><input type="radio" name="mailing_yn" value="N"> 아니오</label>
                    </td>
                </tr>
                <tr>
                    <th>SMS 수신 여부</th>
                    <td colspan="3">
                        <span class="description">뉴스레터, 이벤트 안내 등의 정보를 수신합니다.</span>
                        <label><input type="radio" name="sms_yn" value="Y"> 예</label>
                        <label><input type="radio" name="sms_yn" value="N"> 아니오</label>
                    </td>
                </tr>
            </tbody>
        </table>
        $param[note_html]
        <input type="hidden" id="id_over_yn" name="id_over_yn" value="N">
        <input type="hidden" id="certi_yn" name="certi_yn" value="N">
HTML;

    return $html;
}
?>
