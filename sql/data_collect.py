from bs4 import BeautifulSoup
import urllib.request
import configparser
from datetime import timedelta, date
import time
import urllib.parse
import socket
from socket import timeout
import mysql.connector
from mysql.connector import Error

user_agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64)'
headers = {'User-Agent': user_agent}
#values = {'name': 'Michael Foord',
#          'location': 'Northampton',
#          'language': 'Python' }
#data = urllib.parse.urlencode(values)
#data = data.encode('ascii')

keyword='编辑'


# MySQL 连接配置
mysql_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '123456',
    'database': 'search_engine',
    'port': 3305,  # 默认 MySQL 端口号
}


def get_one_page_news(page_url):
#    page_url='http://www.chinanews.com/scroll-news/2019/0801/news.shtml'
    root='http://www.chinanews.com'
    req = urllib.request.Request(page_url, headers = headers)
    
    try:
        response = urllib.request.urlopen(req, timeout=10)
        html = response.read()
    except socket.timeout as err:
        print('socket.timeout')
        print(err)
        return []
    except Exception as e:
        print("-----%s:%s %s-----"%(type(e),e.reason, page_url))
        return []
    
    soup = BeautifulSoup(html,"html.parser") # http://www.crummy.com/software/BeautifulSoup/bs4/doc.zh/
    
    news_pool = []
    news_list = soup.find('div', class_ = "content_list")
    items = news_list.find_all('li')
    for i,item in enumerate(items):
#        print('%d/%d'%(i,len(items)))
        if len(item) == 0:
            continue
        
        a = item.find('div', class_ = "dd_bt").find('a')
        title = a.string
        url = a.get('href')
        if root in url:
            url=url[len(root):]
        
        category = ''
        try:
            category = item.find('div', class_ = "dd_lm").find('a').string
        except Exception as e:
            continue
        
        if category == '图片':
            continue
        
        year = url.split('/')[-3]
        date_time = item.find('div', class_ = "dd_time").string
        date_time = '%s-%s:00'%(year, date_time)
        
        news_info = [date_time, "http://www.chinanews.com"+url, title]
        news_pool.append(news_info)
    return news_pool

def get_news_pool(start_date, end_date):
    news_pool=[]
    delta = timedelta(days=1)
    while start_date <= end_date:
        date_str=start_date.strftime("%Y/%m%d")
        page_url='http://www.chinanews.com/scroll-news/%s/news.shtml'%(date_str)
        print('Extracting news urls at %s'%date_str)
        news_pool += get_one_page_news(page_url)
#        print('done')
        start_date += delta
    return news_pool

def crawl_news(news_pool, min_body_len):
    # 初始化计数器 i 为 1
    i = 1

    # 遍历新闻池中的每个新闻
    for n, news in enumerate(news_pool):
        print('%d/%d' % (n, len(news_pool)))

        # 构造 HTTP 请求头部信息
        req = urllib.request.Request(news[1], headers=headers)
        try:
            # 尝试打开指定 URL，设置超时时间为 10 秒
            response = urllib.request.urlopen(req, timeout=10)
            html = response.read()
        except socket.timeout as err:
            # 处理超时异常，打印错误信息，等待 1 分钟后继续
            print('socket.timeout')
            print(err)
            print("Sleeping for 1 minute")
            time.sleep(60)
            continue
        except Exception as e:
            # 处理其他异常，打印异常信息，等待 1 秒后继续
            print("--1---%s:%s %s-----" % (type(e), e.reason, news[1]))
            print("Sleeping for 1 second")
            time.sleep(1)
            continue

        # 使用 BeautifulSoup 解析 HTML 内容
        soup = BeautifulSoup(html, "html.parser")  # http://www.crummy.com/software/BeautifulSoup/bs4/doc.zh/
        # 移除 HTML 中的所有脚本标签
        [s.extract() for s in soup('script')]

        try:
            # 尝试找到包含新闻正文的 div 元素，并获取所有段落标签
            ps = soup.find('div', class_="left_zw").find_all('p')
        except Exception as e:
            # 处理异常，打印异常信息，等待 1 分钟后继续
            print("--2---%s: %s-----" % (type(e), news[1]))
            print("Sleeping for 1 minute")
            time.sleep(60)
            continue

        # 初始化新闻正文内容为空字符串
        body = ''
        # 遍历每个段落，获取文本内容并添加到正文中
        for p in ps:
            cur = p.get_text().strip()
            if cur == '':
                continue
            body += '\t' + cur + '\n'
        # 移除正文中的空格
        body = body.replace(" ", "")

        # 过滤掉不包含关键词的新闻
        if keyword not in body:  # 过滤掉乱码新闻
            continue
        # 过滤掉正文长度小于等于 min_body_len 的新闻
        if len(body) <= min_body_len:
            continue

        # 创建 MySQL 连接
        connection = None
        try:
            connection = mysql.connector.connect(**mysql_config)
            cursor = connection.cursor()

            # 插入新闻数据，不包含 id 列
            sql = "INSERT INTO news (url, title, datetime, body) VALUES (%s, %s, %s, %s)"
            val = (news[1], news[2], news[0], body)
            cursor.execute(sql, val)


            # 提交事务
            connection.commit()
            # 更新计数器
            i += 1

            # 每插入 500 条新闻后，等待 3 分钟
            if i % 500 == 0:
                print("Sleeping for 3 minutes")
                time.sleep(180)

        except Error as e:
            # 处理数据库错误
            print("MySQL Error:", e)

        finally:
            # 关闭数据库连接
            if connection:
                connection.close()
    
if __name__ == '__main__':
    #创建时间差对象
    delta = timedelta(days=-5)
    #截止时间设置为当前日期
    end_date = date.today()
    #起始时间设置为五天前
    start_date = end_date + delta

    #存储新闻页面
    news_pool = get_news_pool(start_date, end_date)
    print('Starting to crawl %d news'%len(news_pool))
    crawl_news(news_pool, 140)
    print('done!')