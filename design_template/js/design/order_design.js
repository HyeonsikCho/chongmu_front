$(document).ready(function () {
    //묶음여부
    $('.sheet ._deliveryGroup').on('change', function () {
        deliveryGroupSetting();
    });
    
    deliveryGroupSetting();
    
    //최초 첫번째 받는 주소에 모두 체크
    $('.sheet .delivery table.to:eq(0) ._orderGroup input[type=radio]').attr('checked', true);
    
    //배송지 단일/복수 설정
    $('.sheet .delivery ._toNum input[type=radio]').on('click', function () {
        if ($(this).hasClass('_single')) {
            $(this).closest('ul').find('select')[0].disabled = true;
            toTableSetting(1);
        } else {
            $(this).closest('li').find('select')[0].disabled = false;
            var toNum = Number($(this).closest('ul').find('select').children('option:selected').text());
            toTableSetting(toNum);
        }
    });
    
    $("#multi_to_num").on('change', function () {
        var toNum = Number($(this).children('option:selected').val())
        toTableSetting(toNum);
    });
    
    $('.sheet .delivery ._toNum ._single').click();
    $('#multi_to_num option:first-child').attr('selected', true);
    
    //결제 방법
    if ($('input._paymentType:checked').length == 0) {
        $('input._paymentType._prepaid').attr('checked', true);
    }
    
    //금액 요약
    $('._paymentType input._prepaid').on('click', function () {
        priceSummaryByType();
    });

    priceSummaryByType();
});

//묶음 설정
function deliveryGroupSetting () {
    $('ul._orderGroup li').not('._single').each(function () {
        $(this).find('span._text').html('');
        $(this).removeClass('_on');
    });
    
    $('._deliveryGroup').each(function () {
        var code = $(this).children('option:selected').val();
            
        $('ul._orderGroup li._' + code).addClass('_on').find('span._text').append('<span>' + $(this).closest('tr').find('._name').text() + '</span>');
    });
}

//받는 사람 table 설정
function toTableSetting (toNum) {
    var currentToNum = $('.delivery table.input.to').length,
        deliverySection = $('section.delivery');
    
    if (currentToNum > toNum) {
        deliverySection.find('table.input.to').each(function (i) {
            if (i >= toNum) {
                // 삭제되는 테이블에 속한 라디오 버튼이 사라지면 체크도 없어짐
                // 그래서 삭제되는 라디오 버튼을 맨 위쪽으로 체크해줌
                $(this).find(".orderGroup:checked").each(function() {
                    var name = $(this).attr("name");
                    $("#to_1").find("input[type='radio'][name='" + name + "']")
                              .prop("checked", true);
                });

                $(this).remove();
            }
        });
    } else {
        var tableHtml = $($('.delivery table.input.to')[0]).clone();
        // 회원정보와 동일 부분 삭제
        tableHtml.find('label[member_preset=member_preset]').remove();
        tableHtml.find('input[type=text]').val('');
        tableHtml.find('.orderGroup').attr('checked', false);
        tableHtml.find('select').children('option:first-child').attr('selected', true);

        for (var i = currentToNum; i < toNum; i++) {
            var idx = i + 1;
            var $clone = tableHtml.clone();
            $clone.attr("id", "to_" + idx);
            // 새로운 정보 입력부분 이름변경, check
            $clone.find("input[type='radio'][name='to_1_preset']")
                  .attr({"name"    : "to_" + idx + "_preset",
                         "onclick" : "changeTo('" + idx + "', 'new')"})
                  .prop("checked", true);
            // 주소입력 라디오버튼 바인드 함수 변경
            $clone.find(".postcode_btn").attr("onclick",
                                              "getPostcode('to_" + idx + "');");
            // 나의배송지 선택 바인드 함수 변경
            $clone.find(".dlvr_addr_pop").attr("onclick",
                                               "getPostcode('to_" + idx + "');");
            // 나의배송지로 등록 바인드 함수 변경
            $clone.find(".addressRegist").attr("onclick",
                                               "getPostcode('to_" + idx + "');");
            // 배송방법
            $clone.find("#to_1_dlvr_way").attr(
                {"id"   : "to_" + idx + "_dlvr_way",
                 "name" : "to_" + idx + "_dlvr_way"}
            );
            // 배송비 지불
            $clone.find("input[name='to_1_dlvr_sum_way']").attr(
                {"id"   : "to_" + idx + "_dlvr_sum_way",
                 "name" : "to_" + idx + "_dlvr_sum_way"}
            );
            // 성명/상호
            $clone.find("#to_1_name").attr(
                {"id"   : "to_" + idx + "_name",
                 "name" : "to_" + idx + "_name"}
            );
            // 연락처
            $clone.find("#to_1_tel_num1").attr(
                {"id"   : "to_" + idx + "_tel_num1",
                 "name" : "to_" + idx + "_tel_num1"}
            );
            $clone.find("#to_1_tel_num2").attr(
                {"id"   : "to_" + idx + "_tel_num2",
                 "name" : "to_" + idx + "_tel_num2"}
            );
            $clone.find("#to_1_tel_num3").attr(
                {"id"   : "to_" + idx + "_tel_num3",
                 "name" : "to_" + idx + "_tel_num3"}
            );
            // 휴대전화
            $clone.find("#to_1_cell_num1").attr(
                {"id"   : "to_" + idx + "_cell_num1",
                 "name" : "to_" + idx + "_cell_num1"}
            );
            $clone.find("#to_1_cell_num2").attr(
                {"id"   : "to_" + idx + "_cell_num2",
                 "name" : "to_" + idx + "_cell_num2"}
            );
            $clone.find("#to_1_cell_num3").attr(
                {"id"   : "to_" + idx + "_cell_num3",
                 "name" : "to_" + idx + "_cell_num3"}
            );
            // 주소
            $clone.find("#to_1_zipcode").attr(
                {"id"   : "to_" + idx + "_zipcode",
                 "name" : "to_" + idx + "_zipcode"}
            );
            $clone.find("#to_1_addr").attr(
                {"id"   : "to_" + idx + "_addr",
                 "name" : "to_" + idx + "_addr"}
            );
            $clone.find("#to_1_addr_detail").attr(
                {"id"   : "to_" + idx + "_addr_detail",
                 "name" : "to_" + idx + "_addr_detail"}
            );
            // 주문선택 위치지정
            $clone.find(".orderGroup").attr("pos", "to_" + idx);

            deliverySection.append($clone);
        }
    }
}

// 주문서 - 결제방법 선입금 선택 시 주문부족금액
function priceSummaryByType () {
    if ($('._paymentType input._prepaid')[0] && $('._paymentType input._prepaid')[0].checked) {
        $('.sheet .priceSummary').addClass('_prepaid');
    } else {
        $('.sheet .priceSummary').removeClass('_prepaid');
    }
}
