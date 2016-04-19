#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include<time.h>
#include<unistd.h>
#include<curl/curl.h>
#include <mysql/mysql.h>


//声明函数
int Read_order(void);
int Read_staff(void);
int Match(void);
int Sent(int num);
int Update(int num);
int Check(int num1,int num2);
size_t save_data(void *ptr, size_t size, size_t nmemb, FILE *stream);

//声明全局变量
//数据库相关参数
char host[20] = "localhost";
char DBuser[20] = "fyyf";
char DBpassword[20] = "fyyf2013";
char DBname[20] = "fyyf";
int untreated_order;          //未处理订单数
int available_staff;          //可使用技术员
int order[100][2];            //记录订单[i][0]->order_id [i][1]->vip 由Read_order函数读取并排序
int staff[100][2];            //记录可用技术员[i][0]->staff_id [i][1]->last_time 由Read_staff函数读取并排序
int match[100][2];            //记录匹配好的订单和技术员[i][0]->order-id [i][1]->staff_id 由Match函数匹配并写入
char getdata[200];

size_t save_data(void *ptr, size_t size, size_t nmemb, FILE *stream)
{
	size_t written;
	written = fwrite(ptr, size, nmemb, stream);
	return written;
}

int Read_order(void)
{
	MYSQL* mysql;
	MYSQL_RES* res;
	MYSQL_ROW row;
	MYSQL_RES* res1;
	MYSQL_ROW row1;
	char* temp1;
	int t;
again:
	try
	{
		mysql = mysql_init(NULL);
		if (!mysql_real_connect(mysql, host, DBuser, DBpassword, DBname, 3306, NULL, 0))
		{
			printf("Database connection failed, error reason:\n");
			fprintf(stderr, " %s\n", mysql_error(mysql));
			return 0;
		}
		mysql_set_character_set(mysql, "utf8"); 
		char query[100] = "SELECT order_id FROM `fy_order` where status=0 or status=5";
		mysql_real_query(mysql, query, strlen(query));
		res = mysql_store_result(mysql);
		int i = 0;
		untreated_order = 0;
		while (row = mysql_fetch_row(res))
		{
			for (t = 0; t<mysql_num_fields(res); t++)
			{
				temp1 = row[t];
				order[i][0] = atoi(temp1);
			}
			i++;
			untreated_order = i;
		}
	}
	catch (...)
	{
		printf("%s", "Abnormal database connection!");
		exit(1);
	}
	if (untreated_order == 0)
	{
		printf("No order\n");
		sleep(180);
		goto again;
	}
	for (int j = 0; j<untreated_order; j++)
	{
		if (order[j][0] == -1)
		{
			break;
		}
		char temp2[200] = "SELECT vip FROM `fy_order` where order_id=";
		char temp3[200] = "";
		sprintf(temp3, "%d", order[j][0]);
		strcat(temp2, temp3);
		mysql_real_query(mysql, temp2, strlen(temp2));
		res1 = mysql_store_result(mysql);
		int i = 0;
		while (row1 = mysql_fetch_row(res1))
		{
			for (t = 0; t<mysql_num_fields(res1); t++)
			{
				temp1 = row1[t];
				order[j][1] = atoi(temp1);
			}
			i++;
		}
	}
	printf("%d\n", untreated_order);
	for (int j = 0; j < untreated_order - 1; j++)
	{
		for (int i = 0; i < untreated_order - 1 - j; i++)
		{
			if (order[i][1]<order[i + 1][1])
			{
				int temp2 = 0;
				int temp3 = 0;
				temp2 = order[i][1];
				temp3 = order[i][0];
				order[i][1] = order[i + 1][1];
				order[i][0] = order[i + 1][0];
				order[i + 1][1] = temp2;
				order[i + 1][0] = temp3;
			}
		}
	}
	for (int j = 0; j < untreated_order; j++)
	{
		printf("%d %d\n", order[j][0], order[j][1]);
	}
	mysql_free_result(res);
	mysql_free_result(res1);
	mysql_close(mysql);
	return 0;
}

int Read_staff(void)
{
	MYSQL* mysql;
	MYSQL_RES* res;
	MYSQL_ROW row;
	char* temp1;
	char* temp2;
	int t;
again:
	try
	{
		mysql = mysql_init(NULL);
		if (!mysql_real_connect(mysql, host, DBuser, DBpassword, DBname, 3306, NULL, 0))
		{
			printf("Database connection failed, error reason:\n");
			fprintf(stderr, " %s\n", mysql_error(mysql));
			return 0;
		}
		mysql_set_character_set(mysql, "utf8");
		char query[100] = "SELECT staff_id,last_time FROM `fy_staff` where status=0 and max>0";
		mysql_real_query(mysql, query, strlen(query));
		res = mysql_store_result(mysql);
		int i = 0;
		available_staff = 0;
		while (row = mysql_fetch_row(res))
		{
			for (t = 0; t<mysql_num_fields(res); t++)
			{
				temp1 = row[0];
				staff[i][0] = atoi(temp1);
				temp2 = row[1];
				staff[i][1] = atoi(temp2);
			}
			i++;
			available_staff = i;
		}
	}
	catch (...)
	{
		printf("%s", "Abnormal database connection!");
		exit(1);
	}
	if (available_staff == 0)
	{
		printf("No staff\n");
		sleep(180);
		goto again;
	}
	for (int j = 0; j < available_staff - 1; j++)
	{
		for (int i = 0; i < available_staff - 1 - j; i++)
		{
			if (staff[i][1]>staff[i + 1][1])
			{
				int temp3 = 0;
				int temp4 = 0;
				temp3 = staff[i][1];
				temp4 = staff[i][0];
				staff[i][1] = staff[i + 1][1];
				staff[i][0] = staff[i + 1][0];
				staff[i + 1][1] = temp3;
				staff[i + 1][0] = temp4;
			}
		}
	}
	printf("%d\n", available_staff);
	for (int j = 0; j < available_staff; j++)
	{
		printf("%d %d\n", staff[j][0],staff[j][1]);
	}
	mysql_free_result(res);
	mysql_close(mysql);
	return 0;
}

int Match(void)
{
	int i = 0;                              //每次只分配第一个订单，以便分配一次后重新读取技术员上次分配时间
	match[i][0] = order[i][0];
	for (int j = 0; j < available_staff; j++)
	{
		int flag = 0;                       //0表示技术员可用  1表示不可用
		flag = Check(i,j);
		if (flag==0)                        
		{
			match[i][1] = staff[j][0];
			Sent(i);
			break;
		}
		else
		{
			continue;
		}
	}
	return 0;
}

int Check(int num1, int num2)
{
	int i;
	i = num1;
	int j;
	j = num2;
	MYSQL* mysql;
	MYSQL_RES *res;
	MYSQL_ROW row;
	int temp;
	try
	{
		mysql = mysql_init(NULL);
		if (!mysql_real_connect(mysql, host, DBuser, DBpassword, DBname, 3306, NULL, 0))
		{
			printf("Database connection failed, error reason:\n");
			fprintf(stderr, " %s\n", mysql_error(mysql));
			return 0;
		}
		mysql_set_character_set(mysql, "utf8");
		char query[100] = "SELECT refuse_order_id FROM `fy_staff` where staff_id=";
		char query2[20] = "";
		sprintf(query2, "%d", staff[j][0]);
		strcat(query, query2);
		mysql_real_query(mysql, query, strlen(query));
		res = mysql_store_result(mysql);
		while (row = mysql_fetch_row(res))
		{
			for (int t = 0; t<mysql_num_fields(res); t++)
			{
				temp = atoi(row[0]);
			}
		}
	}
	catch (...)
	{
		printf("%s", "Abnormal database connection!");
		exit(1);
	}
	if (temp==match[i][0])
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

int Sent(int num)
{
	int i;
	i = num;
	//邮件内容
	//用户部分
	//通过computer_id在fy_computer中读取
	char brand[10];         //用户电脑品牌
	char model[20];         //用户电脑型号
	time_t  buy_time;       //购买时间戳
	//通过match[i][0]在fy_order中读取
	char number[20];        //订单编号
	char user_id[20];       //读取用户ID
	char computer_id[20];   //订单关联电脑ID
	char vip_1[10];
	char vip_2[10];         //vip
	//通过match[i][0]在fy_orderextend中读取
	char description[800];  //电脑故障描述
	//通过user_id在fy_userextend中读取
	char user_name[25];     //用户姓名
	char phone[11];         //用户手机
	//技术员部分
	//通过match[i][1]在fy_staff中读取
	char email[64];          //技术员邮箱
	char staff_user_id[20];   //读取技术员的用户ID
	//通过staff_user_id在fy_userextend中读取
	char staff_name[25];      //读取技术员姓名

	//操作数据库
	MYSQL *mysql;
	MYSQL_RES *res;
	MYSQL_ROW row;
	int t;
	try
	{
		mysql = mysql_init(NULL);
		if (!mysql_real_connect(mysql, host, DBuser, DBpassword, DBname, 3306, NULL, 0))
		{
			printf("Database connection failed, error reason \n");
			fprintf(stderr, " %s\n", mysql_error(mysql));
			return 1;
		}
		mysql_set_character_set(mysql, "utf8");
		//fy_order
		char temp[100] = "select number,user_id,computer_id,vip from fy_order where order_id= ";
		char temp2[20] = "";
		sprintf(temp2, "%d", match[i][0]);
		strcat(temp, temp2);
		mysql_query(mysql, temp);
		res = mysql_use_result(mysql);
		while (row = mysql_fetch_row(res))
		{
			for (t = 0; t<mysql_num_fields(res); t++)
			{
				strcpy(number, row[0]);
				strcpy(user_id, row[1]);
				strcpy(computer_id, row[2]);
				strcpy(vip_1, row[3]);
				char isvip[] = "1";
				char tmp1[] = "是";
				char tmp2[] = "否";
				if (strcmp(isvip, vip_1)==0)
				{
					sprintf(vip_2, "%s", tmp1);
				}
				else
				{
					sprintf(vip_2, "%s", tmp2);
				}
			}
		}
		printf("number:%s	", number);
		printf("user_id:%s	", user_id);
		printf("computer_id:%s	", computer_id);
		printf("\n");
		//fy_computer
		char temp3[100] = "select brand,model,buy_time from fy_computer where computer_id= ";
		strcat(temp3, computer_id);
		mysql_query(mysql, temp3);
		res = mysql_use_result(mysql);
		while (row = mysql_fetch_row(res))
		{
			for (t = 0; t<mysql_num_fields(res); t++)
			{
				strcpy(brand, row[0]);
				strcpy(model, row[1]);
				buy_time = atoi(row[2]);
			}
		}
		printf("brand:%s	", brand);
		printf("model:%s	", model);
		printf("buy_time:%ld	", buy_time);
		printf("\n");
		//fy_orderextend
		char temp4[100] = "select description from fy_orderextend where order_id= ";
		char temp5[20] = "";
		sprintf(temp5, "%d", match[i][0]);
		strcat(temp4,temp5);
		mysql_query(mysql, temp4);
		res = mysql_use_result(mysql);
		while (row = mysql_fetch_row(res))
		{
			for (t = 0; t<mysql_num_fields(res); t++)
			{
				strcpy(description, row[0]);
			}
		}
		printf("description:%s	", description);
		printf("\n");
		//fy_userextend    user
		char temp6[100] = "select name,phone from fy_userextend where user_id= ";
		strcat(temp6, user_id);
		mysql_query(mysql, temp6);
		res = mysql_use_result(mysql);
		while (row = mysql_fetch_row(res))
		{
			for (t = 0; t<mysql_num_fields(res); t++)
			{
				strcpy(user_name, row[0]);
				strcpy(phone, row[1]);
			}
		}
		printf("user_name:%s	", user_name);
		printf("phone:%s	", phone);
		printf("\n");
		//fy_staff
		char temp7[100] = "select email,user_id from fy_staff where staff_id= ";
		char temp8[20] = "";
		sprintf(temp8, "%d", match[i][1]);
		strcat(temp7, temp8);
		mysql_query(mysql, temp7);
		res = mysql_use_result(mysql);
		while (row = mysql_fetch_row(res))
		{
			for (t = 0; t<mysql_num_fields(res); t++)
			{
				strcpy(email, row[0]);
				strcpy(staff_user_id, row[1]);
			}
		}
		printf("email:%s	", email);
		printf("staff_user_id:%s	", staff_user_id);
		printf("\n");
		//fy_userextend    staff
		char temp9[100] = "select name from fy_userextend where user_id= ";
		strcat(temp9, staff_user_id);
		mysql_query(mysql, temp9);
		res = mysql_use_result(mysql);
		while (row = mysql_fetch_row(res))
		{
			for (t = 0; t<mysql_num_fields(res); t++)
			{
				strcpy(staff_name, row[0]);
			}
		}
		printf("staff_name:%s	", staff_name);
		printf("\n");
	}
	catch (...)
	{
		printf("%s", "Abnormal database connection!");
		return 1;
	}
	mysql_free_result(res); 
	mysql_close(mysql);
	//title
	char title[200] = "[飞扬维修新单]";
	strcat(title, staff_name);
	strcat(title, number);
	//content
	char content[2048] = "<html>亲爱的技术员天使，你辛苦了。有同学需要你的热心帮助：<br\><br\><br\>报修人姓名：";
	strcat(content, user_name);
	char content_1[30] = "<br\>是否为会员：";
	strcat(content, content_1);
	strcat(content, vip_2);
	char content_2[20] = "<br\>电话：";
	strcat(content, content_2);
	strcat(content, phone);
	char content_3[20] = "<br\>订单号：";
	strcat(content, content_3);
	strcat(content, number);
	char content_4[30] = "<br\>电脑相关信息：";
	strcat(content, content_4);
	strcat(content, brand);
	strcat(content, model);
	char content_5[30] = "<br\>故障描述：";
	strcat(content, content_5);
	strcat(content, description);
	char content_6[200] = "<br\>请您尽快联系报修人，并在四川大学飞扬俱乐部微信回复：接单%20进行确认或取消操作。<br\>谢谢，辛苦了。</html>";
	strcat(content, content_6);
	//整理所有内容
	char send_url[2500] = "localhost/mail/index.php?email=";
	char url_tmp1[] = "&&title=";
	char url_tmp2[] = "&&content=";
	strcat(send_url, email);
	strcat(send_url, url_tmp1);
	strcat(send_url, title);
	strcat(send_url, url_tmp2);
	strcat(send_url, content);
	printf("\n%s\n", send_url);
	//发送部分
	FILE *fp;
	fp = fopen("Getdata.txt", "w+");
	char success[] = "{\"code\":1,\"msg\":\"success\"}";
	CURL *curl;
	curl = curl_easy_init(); // 初始化curl 
	if (curl != NULL)
	{

		curl_easy_setopt(curl, CURLOPT_URL, send_url);// 设置目标url 
		curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, save_data);
		curl_easy_setopt(curl, CURLOPT_WRITEDATA, fp);
		curl_easy_perform(curl);// 执行操作
		curl_easy_cleanup(curl); // 执行回收资源 
		fclose(fp);
	}
	fp = fopen("Getdata.txt", "r");
	fscanf(fp, "%s", getdata);
	if (strcmp(getdata, success) == 0)
	{
		printf("%s\n",getdata);		
		Update(i);
	}
	else
	{
		printf("%s\n",getdata);
	}
	fclose(fp);
}

int Update(int num)
{
	int i;
	i = num;
	MYSQL* mysql;
	try
	{
		mysql = mysql_init(NULL);
		if (!mysql_real_connect(mysql, host, DBuser, DBpassword, DBname, 3306, NULL, 0))
		{
			printf("Database connection failed, error reason:\n");
			fprintf(stderr, " %s\n", mysql_error(mysql));
			return 0;
		}
		mysql_set_character_set(mysql, "utf8");
		//fy_order表更新staff_id
		char temp[100] = "update fy_order set staff_id= ";
		char temp2[100] = " where order_id =";
		char temp3[20] = "";
		char temp4[20] = "";
		sprintf(temp3, "%d", match[i][1]);
		strcat(temp, temp3);
		sprintf(temp4, "%d", match[i][0]);
		strcat(temp2, temp4);
		strcat(temp, temp2);
		if (mysql_query(mysql, temp))
		{
			printf("Order.staff update failed!\n%s\n", mysql_error(mysql));
			exit(1);
		}
		//fy_order表更新status
		char temp5[100] = "update fy_order set status=1 where order_id = ";
		char temp6[20] = "";
		sprintf(temp6, "%d", match[i][0]);
		strcat(temp5, temp6);
		if (mysql_query(mysql, temp5))
		{
			printf("Order.status update failed!\n--reason：\n%s\n", mysql_error(mysql));
			exit(1);
		}
		//fy_staff表更新max
		char temp7[100] = "update fy_staff set max=max-1 where staff_id =";
		char temp8[20] = "";
		sprintf(temp8, "%d", match[i][1]);
		strcat(temp7, temp8);
		if (mysql_query(mysql, temp7))
		{
			printf("Staff.max update failed!\n--reason：\n%s\n", mysql_error(mysql));
			exit(1);
		}
		//fy_order表更新distribute_time
		char temp9[100] = "update fy_order set distribute_time = ";
		char temp10[20] = "";
		char temp11[50] = " where order_id =";
		char temp12[20] = "";
		time_t time_now;
		time_now = time(NULL);
		sprintf(temp10, "%ld", time_now);
		strcat(temp9, temp10);
		sprintf(temp12, "%d", match[i][0]);
		strcat(temp11, temp12);
		strcat(temp9, temp11);
		if (mysql_query(mysql, temp9))
		{
			printf("order.distribute_time update failed!\n--reason：\n%s\n", mysql_error(mysql));
			exit(1);
		}
		//fy_staff表更新last_time
		char temp13[100] = "update fy_staff set last_time = ";
		char temp14[20] = "";
		char temp15[50] = " where staff_id =";
		char temp16[20] = "";
		sprintf(temp14, "%ld", time_now);
		strcat(temp13, temp14);
		sprintf(temp16, "%d", match[i][1]);
		strcat(temp15, temp16);
		strcat(temp13, temp15);
		if (mysql_query(mysql, temp13))
		{
			printf("order.distribute_time update failed!\n--reason：\n%s\n", mysql_error(mysql));
			exit(1);
		}
	}
	catch (...)
	{
		printf("%s", "Abnormal database connection!");
		return 0;
	}

}

int main()
{
	for (;;)
	{
		Read_order();
		Read_staff();
		Match();
		printf("/*************************************/\n Finsh! \n/*************************************/\n");
		sleep(180);
	}
	return 0;
}