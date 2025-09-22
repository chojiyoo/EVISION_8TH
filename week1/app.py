from flask import Flask, request, render_template, redirect

app = Flask(__name__) #Flask 앱 실행

guestbook_entries = [] # 방명록을 저장할 리스트, 서버 메모리에만 저장되므로 서버 재시작시 내용 초기화

# 메인 페이지
@app.route('/') # 사용자가 브라우저에서 "/" 경로로 접속했을 때 실행
def home(): # 함수 정의
    return render_template('index.html', entries=guestbook_entries) # 응답 내용
    # templates/index.html 파일을 렌더링해서 응답으로 전송
    # entries=guestbook_entries -> HTML 템플릿에 방명록 데이터 전달

# 검색 결과 페이지
@app.route('/search')
def search():
    query = request.args.get('q','') # 입력값 검증의 부재
    return f"검색 결과: {query}" # 사용자 입력을 그대로 HTML에 삽입, 보안 취약점

# 방명록 작성 페이지
@app.route('/write', methods=['POST'])
def write():
    name = request.form.get('name', '익명') # 작성자 이름 가져오기 (없으면 기본값 '익명'으로)
    message = request.form.get('message', '') # 메시지 가져오기 (없으면 빈 문자열)

    guestbook_entries.append({'name': name, 'message': message}) # 새로운 방명록 데이터를 딕셔너리로 만들어 리스트에 추가
    return redirect('/') # 작성 후 메인 페이지로 목록을 다시 보여주기

if __name__ == '__main__': # 코드 변경 시 자동으로 서버가 재시작
    app.run(debug=True)